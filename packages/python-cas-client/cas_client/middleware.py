"""
Django and Flask middleware for CAS SSO authentication.
"""

import logging
from typing import Optional, List

logger = logging.getLogger('cas_client')


# =============================================================================
# Django Middleware
# =============================================================================

class DjangoCasMiddleware:
    """
    Django middleware for CAS authentication.

    Add to MIDDLEWARE in settings.py:
        'cas_client.middleware.DjangoCasMiddleware',

    Configure in settings.py:
        CAS_CLIENT = CasClient(...)
        CAS_PROTECTED_PATHS = ['/dashboard', '/admin']
        CAS_LOGIN_URL = '/auth/login'
    """

    def __init__(self, get_response):
        self.get_response = get_response

    def __call__(self, request):
        from django.conf import settings
        from django.shortcuts import redirect
        from django.http import JsonResponse

        protected_paths = getattr(settings, 'CAS_PROTECTED_PATHS', [])
        login_url = getattr(settings, 'CAS_LOGIN_URL', '/auth/login')

        is_protected = any(request.path.startswith(p) for p in protected_paths)

        if is_protected:
            cas_user = request.session.get('cas_user')
            if cas_user:
                request.cas_user = cas_user
            else:
                if request.content_type == 'application/json':
                    return JsonResponse({'error': 'Authentication required'}, status=401)
                return redirect(f'{login_url}?return_url={request.path}')

        response = self.get_response(request)
        return response


def django_role_required(*roles: str):
    """
    Django decorator to require specific roles.

    Usage:
        @django_role_required('admin', 'manager')
        def admin_view(request):
            ...
    """
    from functools import wraps

    def decorator(view_func):
        @wraps(view_func)
        def wrapper(request, *args, **kwargs):
            from django.http import JsonResponse

            cas_user = getattr(request, 'cas_user', None) or request.session.get('cas_user')
            if not cas_user:
                return JsonResponse({'error': 'Authentication required'}, status=401)

            user_roles = set(cas_user.get('roles', []))
            if not user_roles & set(roles):
                return JsonResponse({'error': 'Insufficient permissions'}, status=403)

            return view_func(request, *args, **kwargs)
        return wrapper
    return decorator


# =============================================================================
# Flask Middleware / Decorators
# =============================================================================

def flask_cas_required(cas_client):
    """
    Flask decorator for CAS authentication.

    Usage:
        from cas_client.middleware import flask_cas_required

        @app.route('/dashboard')
        @flask_cas_required(cas)
        def dashboard():
            user = flask.session['cas_user']
            return f'Hello {user["username"]}'
    """
    from functools import wraps

    def decorator(f):
        @wraps(f)
        def wrapper(*args, **kwargs):
            import flask

            cas_user = flask.session.get('cas_user')
            if cas_user:
                flask.g.cas_user = cas_user
                return f(*args, **kwargs)

            # Check Bearer token
            auth = flask.request.headers.get('Authorization', '')
            if auth.startswith('Bearer '):
                token = auth[7:]
                user = cas_client.get_user_from_token(token)
                if not user:
                    user = cas_client.validate_token(token)
                if user:
                    flask.g.cas_user = user
                    flask.session['cas_user'] = user
                    return f(*args, **kwargs)

            if flask.request.is_json:
                return flask.jsonify({'error': 'Authentication required'}), 401
            login_url = flask.current_app.config.get('CAS_LOGIN_URL', '/auth/login')
            return flask.redirect(f'{login_url}?return_url={flask.request.path}')
        return wrapper
    return decorator


def flask_role_required(cas_client, *roles: str):
    """
    Flask decorator for role-based access control.

    Usage:
        @app.route('/admin')
        @flask_cas_required(cas)
        @flask_role_required(cas, 'admin')
        def admin():
            ...
    """
    from functools import wraps

    def decorator(f):
        @wraps(f)
        def wrapper(*args, **kwargs):
            import flask

            cas_user = getattr(flask.g, 'cas_user', None) or flask.session.get('cas_user')
            if not cas_user:
                return flask.jsonify({'error': 'Authentication required'}), 401

            if not cas_client.user_has_any_role(cas_user, list(roles)):
                return flask.jsonify({'error': 'Insufficient permissions'}), 403

            return f(*args, **kwargs)
        return wrapper
    return decorator

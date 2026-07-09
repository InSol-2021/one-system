# Minimal image for the Python/Flask CAS sample.
#
# The sample's only dependency on the CAS SDK is the published `cas-system-client`
# package from PyPI (see requirements.txt), so the build context just needs this
# directory's own files. Build/run from the one-system root (or this directory):
#
#   docker build -f examples/python/Dockerfile -t cas-python-sample .
#   docker run --rm -p 9104:9104 --env-file examples/python/.env cas-python-sample
#
FROM python:3.12-slim

ENV PYTHONUNBUFFERED=1 \
    PYTHONDONTWRITEBYTECODE=1 \
    PORT=9104

WORKDIR /app

# Copy the sample app.
COPY examples/python/ /app/

# Install the sample's deps, including cas-system-client from PyPI.
RUN pip install --no-cache-dir -r requirements.txt

# Writable directory for the local SQLite user store (./data/app.db). The app
# creates the file on first run and seeds the demo accounts; this just ensures
# the directory exists and is writable at runtime.
RUN mkdir -p /app/data
ENV APP_DB_PATH=/app/data/app.db

EXPOSE 9104

# Run with gunicorn for a slightly more production-like server. `app:app` is the
# Flask application object in app.py.
RUN pip install --no-cache-dir gunicorn
CMD ["gunicorn", "--bind", "0.0.0.0:9104", "app:app"]

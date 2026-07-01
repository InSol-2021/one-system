# Minimal image for the Python/Flask CAS sample.
#
# IMPORTANT - build context:
# This sample depends on the LOCAL package at packages/python-cas-client, which
# lives OUTSIDE this directory. So the Docker build context must be the repo's
# "one-system" root (the dir that contains both packages/ and examples/), and
# you point at this Dockerfile with -f. From the one-system root:
#
#   docker build -f examples/python/Dockerfile -t cas-python-sample .
#   docker run --rm -p 9104:9104 --env-file examples/python/.env cas-python-sample
#
FROM python:3.12-slim

ENV PYTHONUNBUFFERED=1 \
    PYTHONDONTWRITEBYTECODE=1 \
    PORT=9104

WORKDIR /app

# Copy the LOCAL package source first so its layer caches independently.
COPY packages/python-cas-client /packages/python-cas-client

# Copy the sample app.
COPY examples/python/ /app/

# Install the sample's deps. requirements.txt references the local package via
# the relative path ../../packages/python-cas-client, which resolves to
# /packages/python-cas-client given the layout above.
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

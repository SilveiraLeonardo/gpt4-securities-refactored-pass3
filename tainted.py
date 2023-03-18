
import os
import re
import shlex
import subprocess
import logging
import logging.handlers
import ssl
import hashlib
import base64
from flask import Flask, request
import logging.config
import json
import hmac
from backports.pbkdf2 import pbkdf2_hmac

app = Flask(__name__)

# Raise an error if the SECRET_KEY environment variable is not set
if 'SECRET_KEY' not in os.environ:
    raise ValueError("SECRET_KEY environment variable not set")

app.config['SECRET_KEY'] = os.environ['SECRET_KEY']

# Setup secure logging
logging_config = {
    'version': 1,
    'formatters': {
        'default': {
            'format': '%(asctime)s - %(name)s - %(levelname)s - %(message)s'
        }
    },
    'handlers': {
        'test_sources_7': {
            'class': 'logging.handlers.RotatingFileHandler',
            'filename': 'test_sources_7.log',
            'maxBytes': 1024,
            'backupCount': 1,
            'formatter': 'default',
            'filters': ['test_sources_7'],
            'secure': ssl.SSLContext(ssl.PROTOCOL_TLS_CLIENT)
        }
    },
    'loggers': {
        'test_sources_7': {
            'level': 'ERROR',
            'handlers': ['test_sources_7']
        }
    }
}

logging.config.dictConfig(logging_config)
logger = logging.getLogger('test_sources_7')


@app.route("/api/<something>")
def test_sources_7(something):

    # Validate the input
    if not re.match('^[a-zA-Z0-9]+$', something):
        return "Invalid input"

    # Sanitize the input
    something = shlex.quote(something)

    # Hash the input
    secret_key = app.config['SECRET_KEY']
    salt = os.urandom(16)
    iterations = 100_000
    key = pbkdf2_hmac('sha256', secret_key.encode(), salt, iterations)
    something_hash = hmac.new(key, something.encode(), hashlib.sha256).hexdigest()

    # Execute the command
    try:
        output = subprocess.check_output(something.split(), stderr=subprocess.DEVNULL)
    except subprocess.CalledProcessError as e:
        logger.error(json.dumps({
            'input_hash': base64.b64encode(something_hash.encode()).decode(),
            'error': base64.b64encode(e.output.encode()).decode()
        }))
        return "Error"

    # Validate the output
    if not re.match('^[a-zA-Z0-9]+$', output.decode()):
        return "Invalid output"

    # Hash the output
    output_hash = hmac.new(key, output, hashlib.sha256).hexdigest()

    # Log the output
    logger.error(json.dumps({
        'input_hash': base64.b64encode(something_hash.encode()).decode(),
        'output_hash': base64.b64encode(output_hash.encode()).decode()
    }))

    return output.decode()

if __name__ == "__main__":
    app.run(debug=False)

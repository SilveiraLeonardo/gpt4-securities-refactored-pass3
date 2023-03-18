
import os
import re
import json
import uuid
import time
from cryptography.fernet import Fernet

from flask import (
    Flask,
    render_template,
    request,
    url_for,
    redirect,
    session,
    escape
)

app = Flask(__name__)

# Use environment variables for sensitive data
FLAG = os.environ['FLAG']
app.secret_key = os.environ['SECRET_KEY']

env = app.jinja_env

fernet = Fernet(os.environ['FERNET_KEY'])
ttl = int(os.environ['TTL'])  # seconds

@app.route("/golem", methods=["GET", "POST"])
def golem():
    if request.method != "POST":
        return redirect(url_for("index"))

    golem = request.form.get("golem") or None

    if golem is not None:
        golem = re.sub(r'[^\w]', '', golem)  # Improved regex

    if "golem" not in session or session['golem'] is None:
        session['golem'] = golem

    if session['golem'] is not None:
        encrypted_data = fernet.encrypt(json.dumps({
            'golem': golem,
            'session_id': str(uuid.uuid4()),
            'expires': int(time.time()) + ttl
        }).encode())

        # Store encrypted data in session
        session['golem'] = encrypted_data

        # Use render_template instead of render_template_string
        return render_template('layout.html', golem=escape(golem))

    return redirect(url_for("index"))

@app.route("/", methods=["GET"])
def index():
    return render_template("main.html")

@app.route('/article', methods=['GET'])
def article():

    if 'name' in request.args:
        page = request.args.get('name')
    else:
        page = 'article'

    # Use case-insensitive search for 'flag'
    if re.search('flag', page, re.IGNORECASE):
        page = 'notallowed.txt'

    # Use abspath to prevent directory traversal attacks
    page_path = os.path.abspath(os.path.join('/home/golem/articles/', page))
    if not page_path.startswith('/home/golem/articles/') or not os.path.isfile(page_path):
        return render_template('error.html', error=404)

    try:
        with open(page_path) as f:
            template = f.read()
    except Exception as e:
        template = e

    return render_template('article.html', template=escape(str(template)))

if __name__ == "__main__":
    app.run(host='0.0.0.0', debug=False)

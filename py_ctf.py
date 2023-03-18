
import os
import json
import time
from flask import (
    Flask,
    render_template,
    request,
    url_for,
    redirect,
    session
)
from flask_session import Session
from werkzeug.utils import secure_filename
from cryptography.fernet import Fernet, InvalidToken
import os.path

app = Flask(__name__)

key = os.environ.get("FERNET_KEY")
if not key:
    raise Exception("FERNET_KEY environment variable is not set or has a weak value")

fernet = Fernet(key)
ttl = 7200  # seconds

app.secret_key = os.environ.get("FLASK_SECRET_KEY")
if not app.secret_key:
    raise Exception("FLASK_SECRET_KEY environment variable is not set or has a weak value")

@app.route("/golem", methods=["GET", "POST"])
def golem():
    if request.method != "POST":
        return redirect(url_for("index"))

    golem = request.form.get("golem") or None

    if golem is not None:
        golem = secure_filename(golem)

    if "golem" not in session or session['golem'] is None:
        session['golem'] = fernet.encrypt(json.dumps({
            'golem': golem,
            'expires': int(time.time()) + ttl
        }).encode())

    template = None

    if session['golem'] is not None:
        try:
            decoded = fernet.decrypt(session['golem'], ttl=ttl)
        except InvalidToken:
            decoded = None

        if decoded:
            session_data = json.loads(decoded)
            if session_data.get('expires') < int(time.time()):
                session_data = {}
            else:
                golem = session_data.get('golem')
                template = render_template("golem_page.html", golem=golem)

        session['golem'] = None

    return template

@app.route("/", methods=["GET"])
def index():
    return render_template("main.html")

@app.route('/article', methods=['GET'])
def article():
    if 'name' in request.args:
        page = secure_filename(request.args.get('name'))
    else:
        page = 'article'

    path = os.path.join("/home/golem/articles", page)
    path = os.path.normpath(path)

    if not path.startswith("/home/golem/articles") or page.find('flag') >= 0:
        page = 'notallowed.txt'

    try:
        template = open('/home/golem/articles/{}'.format(page)).read()
    except Exception:
        template = "An error occurred. Please try again."

    return render_template("article.html", template=template)

if __name__ == "__main__":
    app.config['SESSION_TYPE'] = 'filesystem'
    app.config['SESSION_COOKIE_SECURE'] = True
    app.config['SESSION_COOKIE_HTTPONLY'] = True
    app.config['SESSION_COOKIE_SAMESITE'] = 'Strict'
    Session(app)
    app.run(host='127.0.0.1', debug=False)

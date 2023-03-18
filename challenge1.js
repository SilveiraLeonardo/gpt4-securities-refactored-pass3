
import uuid
import base64
from flask import Flask, request, jsonify, Response
from functools import wraps
from cryptography.fernet import Fernet
from cryptography.hazmat.backends import default_backend
from cryptography.hazmat.primitives import hashes
from cryptography.hazmat.primitives.kdf.pbkdf2 import PBKDF2HMAC

app = Flask(__name__)

USERNAME = "example_user"
PASSWORD = "example_password"

def generate_salt():
    return uuid.uuid4().hex.encode()

def encrypt_token(token, salt):
    kdf = PBKDF2HMAC(
        algorithm=hashes.SHA256(),
        length=32,
        salt=salt,
        iterations=100000,
        backend=default_backend()
    )
    key = base64.urlsafe_b64encode(kdf.derive(token.encode()))
    fernet = Fernet(key)
    return fernet.encrypt(token.encode())

def check_auth(username, password):
    return username == USERNAME and password == PASSWORD

def authenticate():
    return Response("Authentication required", 401, {"WWW-Authenticate": "Basic realm=\"Tokens\""})
 
def basic_auth(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        auth = request.authorization
        if not auth or not check_auth(auth.username, auth.password):
            return authenticate()
        return f(*args, **kwargs)
    return decorated

@app.route('/generate_token', methods=['POST'])
@basic_auth
def generate_token_route():
    token = uuid.uuid4().hex
    salt = generate_salt()
    encrypted_token = encrypt_token(token, salt)

    response_data = {"encrypted_token": encrypted_token, "salt": salt.hex()}
    return jsonify(response_data)

if __name__ == '__main__':
    from gunicorn.app.wsgiapp import WSGIApplication

    app = WSGIApplication()
    app.load_wsgiapp(lambda: app)
    app.run()

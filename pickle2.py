
from __future__ import unicode_literals
from flask import Flask, request, make_response, redirect, url_for, session, flash
from flask import render_template
from flask_wtf import FlaskForm
from wtforms import StringField, validators
from flask_talisman import Talisman
import json
import os
from cryptography.fernet import Fernet

# Generate a secure key
SECRET_KEY = os.urandom(32)

# Initialize encryption key
if not os.path.exists('.secret'):
    with open(".secret", "wb") as f:
        secret = Fernet.generate_key()
        f.write(secret)
with open(".secret", "rb") as f:
    key = f.read().strip()
f = Fernet(key)

app = Flask(__name__)
app.config.from_object(__name__)

talisman = Talisman(app)

class LocationForm(FlaskForm):
    location = StringField("Location", [validators.Length(max=500)])


def set_secure_cookie(response, key, value, max_age=None, **kwargs):
    cookie_value = json.dumps(value)
    encrypted_value = f.encrypt(cookie_value.encode())
    response.set_cookie(key, encrypted_value, httponly=True, secure=True, max_age=max_age, **kwargs)


@app.route('/')
def home():
    remembered_str = "Hello, here's what we remember for you. And you can change, delete, or extend it."
    new_str = 'Hello fellow zombie, have you found a tasty brain and want to remember where? Go right here and enter it:'
    location = request.cookies.get('location')

    if not location:
        return redirect(url_for("clear"))

    decrypted_location = json.loads(f.decrypt(location.encode()).decode())
    return render_template('index.html', txt=remembered_str, location=decrypted_location)


@app.route('/clear')
def clear():
    flash("Reminder cleared!")
    response = redirect(url_for('home'))
    response.set_cookie('location', max_age=0)
    return response


@app.route('/reminder', methods=['POST', 'GET'])
def reminder():
    form = LocationForm()
    if form.validate_on_submit():
        location = form.location.data
        
        if location == '':
            flash("Message cleared, tell us when you have found more brains.")
        else:
            flash("We will remember where you find your brains.")

        response = redirect(url_for('home'))
        set_secure_cookie(response, 'location', location, max_age=3600)
        return response
    else:
        if not request.cookies.get('location'):
            return redirect(url_for("clear"))

        location = json.loads(f.decrypt(request.cookies.get('location').encode()).decode())
        return render_template('reminder.html', form=form, location=location)


if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5051, ssl_context=('path/to/cert.pem', 'path/to/key.pem'))


import base64
import os
import hashlib
import getpass

ITERATIONS = 100000

def generate_password_hash(password: str):
    salt = os.urandom(32)
    password_bytes = password.encode('utf-8')
    hashed_password = hashlib.pbkdf2_hmac('sha256', password_bytes, salt, ITERATIONS, dklen=32)
    password_hash = base64.b64encode(salt + hashed_password).decode('utf-8')
    return password_hash

def verify_password(password: str, password_hash: str) -> bool:
    decoded_hash = base64.b64decode(password_hash)
    salt = decoded_hash[:32]
    stored_password_hash = decoded_hash[32:]

    password_bytes = password.encode('utf-8')
    current_password_hash = hashlib.pbkdf2_hmac('sha256', password_bytes, salt, ITERATIONS, dklen=32)

    return stored_password_hash == current_password_hash
    
def ask_for_password():
    password = getpass.getpass("Please, enter your password: ")
    return password

user_password = ask_for_password()
password_hash = generate_password_hash(user_password)

print("Password hash: ", password_hash)

# Example of verifying the user's password
password_check = ask_for_password()
is_password_valid = verify_password(password_check, password_hash)

print("Is the password entered valid? ", is_password_valid)

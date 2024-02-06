# test_cryptography.py
from cryptography.fernet import Fernet

def test():
    key = Fernet.generate_key()
    cipher_suite = Fernet(key)
    text = b"Hello, World!"
    cipher_text = cipher_suite.encrypt(text)
    plain_text = cipher_suite.decrypt(cipher_text)
    print("Encrypted:", cipher_text)
    print("Decrypted:", plain_text)
    print("Cryptography test successful")

if __name__ == '__main__':
    test()

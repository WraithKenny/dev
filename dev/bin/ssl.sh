#!/bin/bash

# Exit if any command fails
set -e

echo -e "Setting up Local SSL...\n"

RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m' # No Color

# Check for required files.
echo -e "\nChecking required files...\n"
if [ -f ./dev/localhost.ext ]
then
	echo -e "${GREEN}✓${NC} './dev/localhost.ext' exists."
else
	echo -e "${RED}✗ Missing required file: './dev/localhost.ext'.${NC}"
	exit 1
fi

if [ -f ./dev/ssl/ca-opts.conf ]
then
	echo -e "${GREEN}✓${NC} './dev/ssl/ca-opts.conf' exists."
else
	echo -e "${RED}✗ Missing required file: './dev/ssl/ca-opts.conf'.${NC}"
	exit 1
fi

echo -e "\nChecking for local Certificate Authority...\n"
# Create folder if needed.
if [ -d ~/.localssl ]
then
	echo -e "${GREEN}✓${NC} '~/.localssl' exists."
else
	echo -e "${RED}✗${NC} '~/.localssl' not found..."
	echo -e "Creating ~/.localssl ..."
	mkdir -p ~/.localssl
	echo -e "${GREEN}✓${NC} '~/.localssl' created."
fi

# Create localhostCA.key if needed.
if [ -f ~/.localssl/localhostCA.key ]
then
	echo -e "${GREEN}✓${NC} 'localhostCA.key' exists."
else
	echo -e "${RED}✗${NC} 'localhostCA.key' not found..."
	echo -e "Creating 'localhostCA.key' ..."
	openssl genrsa -des3 -out ~/.localssl/localhostCA.key 2048
	echo -e "${GREEN}✓${NC} 'localhostCA.key' created."
fi

# Create localhostCA.pem if needed.
if [ -f ~/.localssl/localhostCA.pem ]
then
	echo -e "${GREEN}✓${NC} 'localhostCA.pem' exists."
else
	echo -e "${RED}✗${NC} 'localhostCA.pem' not found..."
	echo -e "Creating 'localhostCA.pem' ..."
	openssl req -x509 -config ./dev/ssl/ca-opts.conf -new -nodes -key ~/.localssl/localhostCA.key -sha256 -days 8250 -out ~/.localssl/localhostCA.pem
	echo -e "${GREEN}✓${NC} 'localhostCA.pem' created."
	echo -e "Attempting to Trust the CA..."
	sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain ~/.localssl/localhostCA.pem
	echo -e "${GREEN}✓${NC} Trusted the CA!"
fi

# Check if localhostCA.pem is expired or otherwise invalid.
if openssl x509 -checkend 0 -noout -in ~/.localssl/localhostCA.pem
then
	echo -e "${GREEN}✓${NC} Certificate is valid."
else
	echo -e "${RED}✗${NC} Certificate has expired (or is invalid/not found)"
	# Try to renew.
	echo -e "Attempting to renew 'localhostCA.pem' ..."
	openssl x509 -x509toreq -in ~/.localssl/localhostCA.pem -signkey ~/.localssl/localhostCA.key -out ~/.localssl/localhostCA.csr
	openssl x509 -req -days 3650 -in ~/.localssl/localhostCA.csr -signkey ~/.localssl/localhostCA.key -out ~/.localssl/localhostCA.pem
	rm ~/.localssl/localhostCA.csr
	echo -e "Attempting to renew trust for 'localhostCA.pem' ..."
	security find-certificate -c 'Localhost SSL' -a -Z | sudo awk '/SHA-1/{system("security delete-certificate -Z "$NF)}'
	sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain ~/.localssl/localhostCA.pem
fi

echo -e "\nChecking for local files...\n"

# Create folder if needed.
if [ -d ./dev/files ]
then
	echo -e "${GREEN}✓${NC} './dev/files' exists."
else
	echo -e "${RED}✗${NC} './dev/files' not found..."
	echo -e "Creating ./dev/files ..."
	mkdir -p ./dev/files
	echo -e "${GREEN}✓${NC} './dev/files' created."
fi

# Create folder if needed.
if [ -d ./dev/files/ssl ]
then
	echo -e "${GREEN}✓${NC} './dev/files/ssl' exists."
else
	echo -e "${RED}✗${NC} './dev/files/ssl' not found..."
	echo -e "Creating ./dev/files/ssl ..."
	mkdir -p ./dev/files/ssl
	echo -e "${GREEN}✓${NC} './dev/files/ssl' created."
fi

# Create localhost.key if needed.
if [ -f ./dev/files/ssl/localhost.key ]
then
	echo -e "${GREEN}✓${NC} 'localhost.key' exists."
else
	echo -e "${RED}✗${NC} 'localhost.key' not found..."
	echo -e "Creating 'localhost.key' ..."
	openssl genrsa -out ./dev/files/ssl/localhost.key 2048
	echo -e "${GREEN}✓${NC} 'localhost.key' created."
fi

# Create localhost.csr if needed.
if [ -f ./dev/files/ssl/localhost.csr ]
then
	echo -e "${GREEN}✓${NC} 'localhost.csr' exists."
else
	echo -e "${RED}✗${NC} 'localhost.csr' not found..."
	echo -e "Creating 'localhost.csr' ..."
	openssl req -new -config ./dev/ssl/ca-opts.conf -key ./dev/files/ssl/localhost.key -out ./dev/files/ssl/localhost.csr
	echo -e "${GREEN}✓${NC} 'localhost.csr' created."
fi

# Create localhost.crt if needed.
if [ -f ./dev/files/ssl/localhost.crt ]
then
	echo -e "${GREEN}✓${NC} 'localhost.crt' exists."
else
	echo -e "${RED}✗${NC} 'localhost.crt' not found..."
	echo -e "Creating 'localhost.crt' ..."
	openssl x509 -req -in ./dev/files/ssl/localhost.csr -CA ~/.localssl/localhostCA.pem -CAkey ~/.localssl/localhostCA.key -CAcreateserial -out ./dev/files/ssl/localhost.crt -days 825 -sha256 -extfile ./dev/localhost.ext
	echo -e "${GREEN}✓${NC} 'localhost.crt' created."
fi

echo -e "\nFinished."

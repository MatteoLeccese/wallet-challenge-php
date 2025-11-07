# Wallet Challenge

## Overview

This monorepo contains three applications that make up the full Wallet Challenge ecosystem:

- `backend/wallet_db`: Core service that manages users, wallets, transactions, and payment sessions.
- `backend/wallet_api`: Proxy API that exposes public routes and forwards requests to the wallet_db service.
- `frontend`: Web application that consumes the APIs and provides the user interface.

## How to Start Each Project

Each subproject includes its own README file with setup instructions, environment configuration, and usage details.  
Please follow the guides provided in each directory:

### Backend

- [wallet_db README](./backend/wallet_db/README.md)  
- [wallet_api README](./backend/wallet_api/README.md)

### Frontend

- [frontend README](./frontend/README.md)


## Documentation

General technical documentation, including architecture, flows, usage examples, and conventions, is located in:

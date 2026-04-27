# AWS Deployment Guide (Student Plan)

This guide explains how to deploy the SportifyGear project to your AWS EC2 instance.

## 1. Prepare your EC2 Instance
1.  **Launch Instance**: Use `Ubuntu 22.04 LTS`.
2.  **Instance Type**: `t3.micro` (eligible for most student plans).
3.  **Security Group**: Ensure these ports are open:
    *   `22` (SSH)
    *   `80` (HTTP)
    *   `443` (HTTPS)
    *   `3000` (Grafana - Optional, you can close this and use a reverse proxy later)
    *   `9090` (Prometheus - Keep closed to the public)

## 2. Install Docker & Docker Compose
Run these commands on your EC2:
```bash
sudo apt-get update
sudo apt-get install -y docker.io docker-compose
sudo usermod -aG docker $USER
newgrp docker
```

## 3. Deployment Steps
1.  **Clone the Repository**:
    ```bash
    git clone https://github.com/your-username/SportifyGear.git
    cd SportifyGear
    git checkout devops-automation
    ```
2.  **Configure Environment**:
    ```bash
    cp SportifyGear/.env.example SportifyGear/.env
    # Edit the .env file with your production settings if necessary
    ```
3.  **Spin up the Containers**:
    ```bash
    docker-compose up -d --build
    ```
4.  **Run Migrations**:
    ```bash
    docker-compose exec app php artisan migrate --force
    ```

## 4. Accessing your App & Monitoring
*   **Website**: `http://<EC2-PUBLIC-IP>`
*   **Grafana**: `http://<EC2-PUBLIC-IP>:3000` (Default login: `admin` / `admin`)
*   **Prometheus**: `http://<EC2-PUBLIC-IP>:9090`

## 5. Automation (Continuous Deployment)
Once you've verified the setup, you can add your EC2 SSH Key and Host to GitHub Secrets (`AWS_SSH_KEY`, `AWS_HOST`) and update the GitHub Actions workflow to automatically run `docker-compose pull && docker-compose up -d` on every push.

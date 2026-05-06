# 📱 P2P Web Copier
> **Seamless Device Synchronization for the Modern Web**

[![Version](https://img.shields.io/badge/version-1.1.0-blue.svg)](https://github.com/Niccher/PC-to-Phone-Copier-Web)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Platform: Web](https://img.shields.io/badge/platform-Web-orange.svg)]()
[![Platform: Docker](https://img.shields.io/badge/platform-Docker-blue.svg)]()
[![PHP: 8.2](https://img.shields.io/badge/PHP-8.2-777bb4.svg)](https://www.php.net/)
[![Framework: CodeIgniter 4](https://img.shields.io/badge/Framework-CodeIgniter%204-ee4323.svg)](https://codeigniter.com/)

P2P Web Copier enables lightning-fast, secure synchronization of files and text snippets between desktop and mobile devices via a unified, containerized web interface. It serves developers, power users, and anyone needing a private, self-hosted solution for moving data between devices without relying on third-party cloud services.

---

## ✨ Features

Every aspect of **P2P Web Copier** is built for speed, security, and a premium user experience.

*   **🚀 P2P File Synchronization**: High-speed file transfers between connected devices with real-time status tracking and visual excellence.
*   **📝 Instant Text Sharing**: Securely share text snippets, links, and notes across multiple sessions instantly.
*   **⚡ Real-Time Sync via SSE**: Leverages Server-Sent Events (SSE) for zero-refresh UI updates—changes appear instantly across all paired devices.
*   **📱 QR Code Device Pairing**: Quickly pair your mobile phone or another browser session by scanning a dynamically generated QR code.
*   **🐳 Full Docker Orchestration**: Production-ready environment using Docker Compose with PHP 8.2-Apache, MySQL 8.0, and phpMyAdmin.
*   **🔒 Advanced Security Hardening**: Implements global CSRF protection, secure directory permissions, and session hardening for private data sharing.
*   **⏳ 30-Day Persistent Sessions**: Extended session longevity (up to 30 days) ensures your devices stay paired even after closing the browser.
*   **♻️ Trash & Recovery System**: Integrated trash management allows you to restore accidentally deleted files or permanently wipe data.
*   **🛠 Developer Diagnostics**: Built-in debug tools to monitor session states, database records, and upload integrity in real-time.
*   **🧹 Zero-Junk Architecture**: Optimized Docker build process with strict `.dockerignore` rules for a lean and fast application footprint.

---

## 🚀 Installation & Setup

Setting up the entire environment is streamlined through Docker Compose.

### Prerequisites
- **Docker Desktop** (v20.10 or higher)
- **Docker Compose**

### Quick Start
1.  **Clone the repository:**
    ```bash
    git clone https://github.com/Niccher/PC-to-Phone-Copier-Web.git
    cd PC-to-Phone-Copier-Web
    ```
2.  **Generate your environment file:**
    ```bash
    cp .env.docker .env
    ```
3.  **Build and start the containers:**
    ```bash
    docker-compose up -d --build
    ```

---

## 📖 Usage

### Initial Pairing
1. Open the web app on your primary device (PC).
2. Scan the generated QR code using your mobile device.
3. Your devices are now paired via a unique session ID valid for 30 days.

### Access Points
| Service | URL | Port |
| :--- | :--- | :--- |
| **P2P Web App** | [http://localhost:8080](http://localhost:8080) | 8080 |
| **phpMyAdmin** | [http://localhost:8081](http://localhost:8081) | 8081 |

---

## ⚙️ Configuration

### Environment Variables
Edit the `.env` file to customize your setup:
- `database.default.hostname`: Set to `db` (Docker service name).
- `app.baseURL`: Update this if deploying to a custom domain.
- `encryption.key`: Generate a secure key for session data.

### Database Migrations
To update your schema inside the container:
```bash
docker exec -it ci4_app php spark migrate
```

---

## 🛠 Technologies Used

![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![CodeIgniter](https://img.shields.io/badge/codeigniter-%23EF4223.svg?style=for-the-badge&logo=codeigniter&logoColor=white)
![Docker](https://img.shields.io/badge/docker-%230db7ed.svg?style=for-the-badge&logo=docker&logoColor=white)
![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white)
![jQuery](https://img.shields.io/badge/jquery-%230769AD.svg?style=for-the-badge&logo=jquery&logoColor=white)
![Bootstrap](https://img.shields.io/badge/bootstrap-%238511FA.svg?style=for-the-badge&logo=bootstrap&logoColor=white)

---

## 🤝 Contributing

We welcome contributions to make P2P Web Copier even better!
1. Fork the project.
2. Create your feature branch (`git checkout -b feature/AmazingFeature`).
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4. Push to the branch (`git push origin feature/AmazingFeature`).
5. Open a Pull Request.

---

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

---
Built with ❤️ by **Niccher Inc**

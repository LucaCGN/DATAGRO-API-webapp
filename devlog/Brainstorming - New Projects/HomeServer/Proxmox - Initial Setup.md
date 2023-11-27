Given your server specs and requirements, running both the web server and the email server on the same VM or using Docker containers are both viable solutions. However, using Docker containers might be the more efficient and scalable approach, as it allows for better resource management, easy updates, and separation of services without the overhead of multiple VMs.

Here's how you can set up your home server using Docker containers on Proxmox:

1. **Proxmox Initial Setup**:
   - Install Proxmox on your home server.
   - Ensure your server has a static IP on your network and configure your router for port forwarding if you want your services accessible from the internet.

2. **Create a VM for Docker**:
   - Within Proxmox, create a VM that will host all Docker containers.
   - Install a minimal Linux OS like Ubuntu Server or Alpine Linux on this VM.
   - Assign enough resources (RAM, CPU) to this VM to handle both the web and email services.

3. **Install Docker and Docker Compose**:
   - On the newly created VM, install Docker and Docker Compose, which will be used to manage your containers.

4. **Set Up Docker Containers**:
   - Create a `docker-compose.yml` file to define and run your multi-container Docker applications.
   - Define services for your web server (e.g., Nginx or Apache for lunara.ai) and email server (e.g., mail server stack including Postfix, Dovecot, and a management interface like Mailcow or Poste.io).

5. **Configure the Web Server Container**:
   - Set up a Docker container for your web server with mounted volumes for your website files.
   - Configure SSL using Let's Encrypt for HTTPS.

6. **Configure the Email Server Container**:
   - Set up Docker containers for your email server components.
   - Include all necessary configurations for handling email for @lunara.com.
   - Configure DNS records (MX, SPF, DKIM, and DMARC) to point to your home server’s IP address.

7. **DNS Configuration**:
   - Update DNS A records for lunara.ai to point to your home server's public IP address.
   - Update MX and other relevant DNS records for lunara.com to point to the email server container.

8. **Security**:
   - Secure your Docker host VM with a firewall (UFW or iptables).
   - Regularly update your Docker images and host OS.
   - Implement fail2ban on the host VM to protect against brute force attacks.

9. **Maintenance and Backups**:
   - Set up cron jobs for regular backups of your web and email data.
   - Monitor logs and performance to ensure services are running smoothly.

10. **Documentation and Support**:
   - Document your configuration and setup for future reference and troubleshooting.
   - Keep an eye on community support forums for Docker, Proxmox, and the software stacks you choose for updates and security notices.


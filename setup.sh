#!/bin/bash
# TaskBB Setup Script for Linux/Mac
# Make executable: chmod +x setup.sh

echo "=== TaskBB Setup Script ==="

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# 1. Create necessary directories
echo -e "\n${YELLOW}1. Creating upload directories...${NC}"
mkdir -p public/uploads/{avatars,reports,tasks}
mkdir -p Views/uploads
echo -e "${GREEN}  ✓ Directories created${NC}"

# 2. Set permissions
echo -e "\n${YELLOW}2. Setting permissions...${NC}"
chmod -R 755 public/uploads
chmod -R 755 Views/uploads
echo -e "${GREEN}  ✓ Permissions set (755)${NC}"

# 3. Set owner (if running as root)
if [ "$EUID" -eq 0 ]; then
    echo -e "\n${YELLOW}3. Setting owner...${NC}"
    
    # Detect web server user
    if id "www-data" >/dev/null 2>&1; then
        WEB_USER="www-data"
    elif id "nginx" >/dev/null 2>&1; then
        WEB_USER="nginx"
    elif id "apache" >/dev/null 2>&1; then
        WEB_USER="apache"
    else
        WEB_USER="$SUDO_USER"
    fi
    
    chown -R $WEB_USER:$WEB_USER public/uploads
    chown -R $WEB_USER:$WEB_USER Views/uploads
    echo -e "${GREEN}  ✓ Owner set to $WEB_USER${NC}"
fi

# 4. Setup .env file
echo -e "\n${YELLOW}4. Checking .env file...${NC}"
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        echo -e "${GREEN}  ✓ Created .env from .env.example${NC}"
    else
        echo -e "${RED}  ✗ .env.example not found!${NC}"
    fi
else
    echo -e "${GREEN}  ✓ .env already exists${NC}"
fi

# 5. Summary
echo -e "\n${GREEN}=== Setup Complete ===${NC}"
echo "Next steps:"
echo "1. Edit .env file and configure database"
echo "2. Import database: mysql -u username -p database_name < taskbb1_latest.sql"
echo "3. Configure your web server (Apache/Nginx)"
echo "4. Access your application"
echo ""

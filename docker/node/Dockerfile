FROM node:16

USER root

##zsh
RUN apt-get update && apt-get install -y zsh fonts-powerline

# Arguments defined in docker-compose.yml
ARG uid

# Create system user to run node
RUN usermod -u $uid node
RUN mkdir -p /home/node/.cache && chown -R node /home/node

# Set working directory
WORKDIR /var/www

USER node

RUN sh -c "$(curl -fsSL https://github.com/deluan/zsh-in-docker/releases/download/v1.1.1/zsh-in-docker.sh)" -- \
   -t powerlevel10k/powerlevel10k  \
   -p yarn  \
   -p node  \
   -p zsh-autosuggestions  \
   -p https://github.com/zsh-users/zsh-autosuggestions  \
   -p zsh-syntax-highlighting  \
   -p https://github.com/zsh-users/zsh-syntax-highlighting  \
   -a "export TERM=xterm-256color"

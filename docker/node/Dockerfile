FROM node:16

USER root

##zsh
RUN apt-get update && apt-get install -y zsh fonts-powerline

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Create system user to run node
RUN userdel -r node
RUN useradd -G root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.cache && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

USER $user

RUN sh -c "$(curl -fsSL https://github.com/deluan/zsh-in-docker/releases/download/v1.1.1/zsh-in-docker.sh)" -- \
   -t powerlevel10k/powerlevel10k  \
   -p yarn  \
   -p node  \
   -p zsh-autosuggestions  \
   -p https://github.com/zsh-users/zsh-autosuggestions

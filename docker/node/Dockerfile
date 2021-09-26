FROM node:16

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Create system user to run node
RUN userdel -r node
RUN useradd -G root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.npm && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

USER $user

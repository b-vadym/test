FROM node:16

USER root

##zsh
RUN apt-get update && apt-get install -y zsh fonts-powerline nano

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
   -a "export TERM=xterm-256color" && \
    echo "export HISTFILE=/home/node/zsh/.zsh_history\n$(cat /home/node/.zshrc)" > /home/node/.zshrc && \
    echo "POWERLEVEL9K_LEFT_PROMPT_ELEMENTS=(user dir node_version newline)\n\
POWERLEVEL9K_COMMAND_EXECUTION_TIME_BACKGROUND=3\n\
POWERLEVEL9K_COMMAND_EXECUTION_TIME_FOREGROUND=0\n\
POWERLEVEL9K_RIGHT_PROMPT_ELEMENTS=(status command_execution_time time)\n\
POWERLEVEL9K_MULTILINE_FIRST_PROMPT_GAP_CHAR='─'\n\
POWERLEVEL9K_MULTILINE_FIRST_PROMPT_GAP_FOREGROUND=244\n\
" >> /home/node/.zshrc

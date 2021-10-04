FROM php:8.0-fpm

# Arguments defined in docker-compose.yml
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nano  \
#  zsh
    zsh \
    fonts-powerline && \
#   Install PHP extensions
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath intl && \
#    redis
    pecl install -o -f redis  && \
    docker-php-ext-enable redis && \
#    xdebug
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
#    clear
    apt-get clean &&  \
    rm -rf /var/lib/apt/lists/* && \
    rm -rf /tmp/pear && \
#    add user
    useradd -G www-data,root -u $uid -d /home/app app && \
    mkdir -p /home/app && \
    chown -R app:app /home/app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

USER app

RUN sh -c "$(curl -fsSL https://github.com/deluan/zsh-in-docker/releases/download/v1.1.1/zsh-in-docker.sh)" -- \
   -t powerlevel10k/powerlevel10k  \
   -p composer  \
   -p symfony2  \
   -p zsh-autosuggestions  \
   -p https://github.com/zsh-users/zsh-autosuggestions  \
   -p zsh-syntax-highlighting  \
   -p https://github.com/zsh-users/zsh-syntax-highlighting  \
   -a "export TERM=xterm-256color" \
   -a "export PATH=\$HOME/bin:\$PATH" && \
    echo "export HISTFILE=/home/app/zsh/.zsh_history\n$(cat /home/app/.zshrc)" > /home/app/.zshrc && \
    echo "POWERLEVEL9K_LEFT_PROMPT_ELEMENTS=(user dir php_version newline)\n\
POWERLEVEL9K_COMMAND_EXECUTION_TIME_BACKGROUND=3\n\
POWERLEVEL9K_COMMAND_EXECUTION_TIME_FOREGROUND=0\n\
POWERLEVEL9K_RIGHT_PROMPT_ELEMENTS=(status command_execution_time time)\n\
POWERLEVEL9K_MULTILINE_FIRST_PROMPT_GAP_CHAR='─'\n\
POWERLEVEL9K_MULTILINE_FIRST_PROMPT_GAP_FOREGROUND=244\n\
" >> /home/app/.zshrc

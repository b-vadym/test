FROM php:8.0-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nano

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath
RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis
##zsh
RUN apt-get update && apt-get install -y zsh fonts-powerline

##xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
# Get latest Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Symfony commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

USER $user

RUN sh -c "$(curl -fsSL https://github.com/deluan/zsh-in-docker/releases/download/v1.1.1/zsh-in-docker.sh)" -- \
   -t powerlevel10k/powerlevel10k  \
   -p composer  \
   -p symfony2  \
   -p zsh-autosuggestions  \
   -p https://github.com/zsh-users/zsh-autosuggestions

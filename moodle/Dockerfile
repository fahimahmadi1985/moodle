# Set up the base image to Ubuntu 22.04
FROM php:apache
FROM ubuntu:22.04


# Update package list
# Set noninteractive installation
ENV DEBIAN_FRONTEND=noninteractive

# Set the timezone
RUN echo "Etc/UTC" > /etc/timezone

RUN apt-get update

# Install the necessary system packages
RUN apt-get install -y software-properties-common
RUN add-apt-repository -y universe
RUN apt-get install -y apache2 php libapache2-mod-php git graphviz aspell ghostscript clamav php-pspell php-curl php-gd php-intl php-mysql php-xml php-xmlrpc php-ldap php-zip php-soap php-mbstring

# Copy Moodle to image
COPY . /var/www/html/moodle/

# Copy custom php.ini file
COPY php.ini /etc/php/8.1/apache2/php.ini

# Give permissions for moodledata
RUN mkdir /var/moodledata && chown -R www-data /var/moodledata && chmod -R 777 /var/moodledata

# Give permissions for moodle directory
RUN chmod -R 0755 /var/www/html/moodle

# Enable apache mod_rewrite
RUN a2enmod rewrite

# Change apache configuration to be able to access Moodle via browser
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# Copy moodle config file
COPY config.php /var/www/html/moodle

# Expose the port apache is reachable on
EXPOSE 80

# Finally, start apache in the foreground
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
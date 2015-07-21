FROM ubuntu:latest
MAINTAINER Loyal Johnson <loyalj@gmail.com>

# Update apt repo data
RUN apt-get update
RUN apt-get -yq upgrade

# Install packages
RUN DEBIAN_FRONTEND=noninteractive apt-get -yq install \
        curl \
        apache2 \
        libapache2-mod-php5 \
        php5-mysql \
        php5-gd \
        php5-curl \
        php-pear \
        php-apc \
        php5-mongo \
        mongodb-clients

# Save space by removing apt data after we're done with it
RUN rm -rf /var/lib/apt/lists/*

# Set the server name in apache config
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Whatever
RUN sed -i "s/variables_order.*/variables_order = \"EGPCS\"/g" /etc/php5/apache2/php.ini

# Disable HTACCESS overwrite by default
ENV ALLOW_OVERRIDE true

# Add image configuration and scripts
ADD run.sh /run.sh
RUN chmod 755 /*.sh

# Configure /app folder with sample app
RUN mkdir -p /app 
ADD app/ /app
RUN rm -fr /var/www/html 
RUN ln -s /app/www /var/www/html

# ADD app/ /app

EXPOSE 80
WORKDIR /app
CMD ["/run.sh"]

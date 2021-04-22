FROM debian:buster

RUN apt update && apt install -y \
    default-mysql-server \
    php7.3 \
    php7.3-mysql \
    php7.3-xml \
    php7.3-gd \
    php7.3-zip \
    composer \
    && true

CMD ["bash"]

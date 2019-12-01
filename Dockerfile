FROM debian:8

RUN apt update
RUN apt install -y apache2
RUN apt install -y php5

RUN echo "mysql-server mysql-server/root_password password root" | debconf-set-selections
RUN echo "mysql-server mysql-server/root_password_again password root" | debconf-set-selections
RUN apt install -y mysql-server
RUN apt install -y php5-mysqlnd

CMD ["bash"]

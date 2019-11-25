FROM debian:8

RUN apt update
RUN apt install -y apache2
RUN apt install -y php5

CMD ["bash"]

FROM ubuntu:latest
LABEL authors="siman"

ENTRYPOINT ["top", "-b"]

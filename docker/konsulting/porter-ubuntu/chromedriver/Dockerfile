FROM alpine:3.8

ENV WHITELISTED_IPS=""

RUN apk add --update --no-cache chromium-chromedriver chromium

EXPOSE 9515

CMD ["chromedriver", "--whitelisted-ips", "echo ${WHITELISTED_IPS}", "--port=9515"]

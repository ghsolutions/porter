FROM alpine:3.7

RUN apk add --update --no-cache nginx && mkdir -p /run/nginx

EXPOSE 80 443

STOPSIGNAL SIGTERM

CMD ["nginx", "-g", "daemon off;"]

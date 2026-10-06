FROM ipeos/spip:4.4.25

LABEL org.opencontainers.image.title="Jeunesse Libertaire" \
      org.opencontainers.image.description="Média participatif d'éducation populaire basé sur SPIP" \
      org.opencontainers.image.licenses="GPL-3.0-or-later"

COPY --chmod=0755 docker/jeunesse-start /usr/local/bin/jeunesse-start
CMD ["jeunesse-start"]

ARG SPIP_VERSION=4.4
FROM ipeos/spip:${SPIP_VERSION}

LABEL org.opencontainers.image.title="Jeunesse Libertaire" \
      org.opencontainers.image.description="Média participatif d'éducation populaire basé sur SPIP" \
      org.opencontainers.image.licenses="GPL-3.0-or-later"

# La version SPIP est paramétrée avec SPIP_VERSION. Le tag 4.4 suit les
# correctifs de la branche ; épinglez un tag précis pour des builds figés.

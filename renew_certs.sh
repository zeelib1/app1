   #!/bin/bash
   certbot renew --quiet --standalone
   docker restart nginx
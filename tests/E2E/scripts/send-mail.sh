#!/bin/bash

FAILURES=$(cat "$1" | jq -r '.failures')

if [ "$FAILURES" = '0' ]; then
  echo "Nothing to send"
  exit 0
fi

SUBJECT="Oops Something went wrong with tests (╯°□°)╯︵ ┻━┻"
TEMPLATE="
<p>
Look like someone breaks the rules and worst, the tests ಠ_ಠ'
</p>

<p>
  <strong>${FAILURES}</strong> tests are broken!
</p>
"


echo $TEMPLATE | mail -s "${SUBJECT}" "${MAILTO}"

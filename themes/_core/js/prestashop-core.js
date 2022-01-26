import prestashop from 'prestashop';
// eslint-disable-next-line
import EventEmitter from "events";

/* eslint-disable */
// "inherit" EventEmitter
for (const i in EventEmitter.prototype) {
    prestashop[i] = EventEmitter.prototype[i];
}


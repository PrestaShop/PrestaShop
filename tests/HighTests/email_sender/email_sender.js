'use strict';

var fs = require('fs');
var optionsBrowser16 = require('../test/itg/1.6/common.webdriverio');
var optionsBrowser17 = require('../test/itg/1.7/common.webdriverio');

// create reusable transporter object using the default SMTP transport
var nodeMailer = require('nodemailer');
var dateFormat = require('dateformat');

// get travis env variable
var senderEmail = process.env.SENDER_EMAIL;
var senderPassword = process.env.SENDER_PASSWORD;
var recipientEmail = process.env.RECIPIENT_EMAIL;

var prestaVersion = new Array();
prestaVersion = [1.6, 1.7];

var transporter = nodeMailer.createTransport({
    service: 'Gmail',
    auth: {
        user: senderEmail,
        pass: senderPassword
    }
});

console.log('Sending Email .....');
var day = dateFormat("yyyy-mm-dd h:MM:ss");

if ((fs.existsSync("email_sender/test_report_" + prestaVersion[0] + ".html")) && (fs.existsSync("email_sender/test_report_" + prestaVersion[1] + ".html"))) {
    transporter.sendMail({
        from: senderEmail, // sender address
        to: recipientEmail, // list of receivers
        subject: '[QA][Test] Bilan des tests - ' + day, // Subject line
        html: "Bonjour,</br>" +
        "</br>" +
        "<br>Les résultats de l'exécution des tests automatisés <b>(Node.js)</b> sur le(s) navigateur(s) <b>" + optionsBrowser16.browser() + "</b> et <b>" + optionsBrowser17.browser() + "</b> sont en pièce jointe.</br> " +
        "</br>" +
        "<br>Bien à vous,</br>" +
        "<br><i>Equipe QA</i></br>", // html body
        attachments: [
            {
                path: "email_sender/test_report_" + prestaVersion[0] + ".html" // stream this file,
            }, {
                filename: "test_report_" + prestaVersion[1] + ".html",
                path: "email_sender/test_report_" + prestaVersion[1] + ".html"
            }
        ]
    });
} else if (fs.existsSync("email_sender/test_report_" + prestaVersion[0] + ".html")) {
    transporter.sendMail({
        from: senderEmail, // sender address
        to: recipientEmail, // list of receivers
        subject: "[QA][Test] Bilan des tests " + prestaVersion[0] + " - " + day, // Subject line
        html: "Bonjour,</br>" +
        "</br>" +
        "<br>Les résultats de l'exécution des tests automatisés <b>(Node.js)</b> sur le navigateur <b>" + optionsBrowser16.browser() + "</b> sont en pièce jointe.</br> " +
        "</br>" +
        "<br>Bien à vous,</br>" +
        "<br><i>Equipe QA</i></br>", // html body
        attachments: [
            {
                path: "email_sender/test_report_" + prestaVersion[0] + ".html" // stream this file,
            }
        ]
    });
} else {
    transporter.sendMail({
        from: senderEmail, // sender address
        to: recipientEmail, // list of receivers
        subject: "[QA][Test] Bilan des tests " + prestaVersion[1] + " - " + day, // Subject line
        html: "Bonjour,</br>" +
        "</br>" +
        "<br>Les résultats de l'exécution des tests automatisés <b>(Node.js)</b> sur le navigateur <b>" + optionsBrowser17.browser() + "</b> sont en pièce jointe.</br> " +
        "</br>" +
        "<br>Bien à vous,</br>" +
        "<br><i>Equipe QA</i></br>", // html body
        attachments: [
            {
                path: "email_sender/test_report_" + prestaVersion[1] + ".html" // stream this file,
            }
        ]
    });
}
console.log("Email successfully sent!")


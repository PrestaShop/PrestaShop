/* jshint quotmark: double */
/* This file contains a dump of CLDR catalogs to bypass CLDR loading of the catalogs through ajax calls */

var fakeEnNumbersCatalog = {
			"main": {
			    "en-US": {
			      "identity": {
			        "version": {
			          "_cldrVersion": "26",
			          "_number": "$Revision: 10669 $"
			        },
			        "generation": {
			          "_date": "$Date: 2014-07-23 16:10:33 -0500 (Wed, 23 Jul 2014) $"
			        },
			        "language": "en",
			        "territory": "US"
			      },
			      "numbers": {
			        "defaultNumberingSystem": "latn",
			        "otherNumberingSystems": {
			          "native": "latn"
			        },
			        "minimumGroupingDigits": "1",
			        "symbols-numberSystem-latn": {
			          "decimal": ".",
			          "group": ",",
			          "list": ";",
			          "percentSign": "%",
			          "plusSign": "+",
			          "minusSign": "-",
			          "exponential": "E",
			          "superscriptingExponent": "×",
			          "perMille": "‰",
			          "infinity": "∞",
			          "nan": "NaN",
			          "timeSeparator": ":"
			        },
			        "decimalFormats-numberSystem-latn": {
			          "standard": "#,##0.###",
			          "long": {
			            "decimalFormat": {
			              "1000-count-one": "0 thousand",
			              "1000-count-other": "0 thousand",
			              "10000-count-one": "00 thousand",
			              "10000-count-other": "00 thousand",
			              "100000-count-one": "000 thousand",
			              "100000-count-other": "000 thousand",
			              "1000000-count-one": "0 million",
			              "1000000-count-other": "0 million",
			              "10000000-count-one": "00 million",
			              "10000000-count-other": "00 million",
			              "100000000-count-one": "000 million",
			              "100000000-count-other": "000 million",
			              "1000000000-count-one": "0 billion",
			              "1000000000-count-other": "0 billion",
			              "10000000000-count-one": "00 billion",
			              "10000000000-count-other": "00 billion",
			              "100000000000-count-one": "000 billion",
			              "100000000000-count-other": "000 billion",
			              "1000000000000-count-one": "0 trillion",
			              "1000000000000-count-other": "0 trillion",
			              "10000000000000-count-one": "00 trillion",
			              "10000000000000-count-other": "00 trillion",
			              "100000000000000-count-one": "000 trillion",
			              "100000000000000-count-other": "000 trillion"
			            }
			          },
			          "short": {
			            "decimalFormat": {
			              "1000-count-one": "0K",
			              "1000-count-other": "0K",
			              "10000-count-one": "00K",
			              "10000-count-other": "00K",
			              "100000-count-one": "000K",
			              "100000-count-other": "000K",
			              "1000000-count-one": "0M",
			              "1000000-count-other": "0M",
			              "10000000-count-one": "00M",
			              "10000000-count-other": "00M",
			              "100000000-count-one": "000M",
			              "100000000-count-other": "000M",
			              "1000000000-count-one": "0B",
			              "1000000000-count-other": "0B",
			              "10000000000-count-one": "00B",
			              "10000000000-count-other": "00B",
			              "100000000000-count-one": "000B",
			              "100000000000-count-other": "000B",
			              "1000000000000-count-one": "0T",
			              "1000000000000-count-other": "0T",
			              "10000000000000-count-one": "00T",
			              "10000000000000-count-other": "00T",
			              "100000000000000-count-one": "000T",
			              "100000000000000-count-other": "000T"
			            }
			          }
			        },
			        "scientificFormats-numberSystem-latn": {
			          "standard": "#E0"
			        },
			        "percentFormats-numberSystem-latn": {
			          "standard": "#,##0%"
			        },
			        "currencyFormats-numberSystem-latn": {
			          "currencySpacing": {
			            "beforeCurrency": {
			              "currencyMatch": "[:^S:]",
			              "surroundingMatch": "[:digit:]",
			              "insertBetween": " "
			            },
			            "afterCurrency": {
			              "currencyMatch": "[:^S:]",
			              "surroundingMatch": "[:digit:]",
			              "insertBetween": " "
			            }
			          },
			          "accounting": "¤#,##0.00;(¤#,##0.00)",
			          "standard": "¤#,##0.00",
			          "unitPattern-count-one": "{0} {1}",
			          "unitPattern-count-other": "{0} {1}"
			        },
			        "miscPatterns-numberSystem-latn": {
			          "atLeast": "{0}+",
			          "range": "{0}–{1}"
			        }
			      }
			    }
			  }
			};

var fakeSupplementalLikelySubtagsCatalog = {
		  "supplemental": {
			    "version": {
			      "_cldrVersion": "26",
			      "_number": "$Revision: 10969 $"
			    },
			    "generation": {
			      "_date": "$Date: 2014-09-11 12:17:53 -0500 (Thu, 11 Sep 2014) $"
			    },
			    "likelySubtags": {
			      "aa": "aa-Latn-ET",
			      "ab": "ab-Cyrl-GE",
			      "abr": "abr-Latn-GH",
			      "ace": "ace-Latn-ID",
			      "ach": "ach-Latn-UG",
			      "ady": "ady-Cyrl-RU",
			      "ae": "ae-Avst-IR",
			      "aeb": "aeb-Arab-TN",
			      "af": "af-Latn-ZA",
			      "agq": "agq-Latn-CM",
			      "ak": "ak-Latn-GH",
			      "akk": "akk-Xsux-IQ",
			      "aln": "aln-Latn-XK",
			      "alt": "alt-Cyrl-RU",
			      "am": "am-Ethi-ET",
			      "amo": "amo-Latn-NG",
			      "aoz": "aoz-Latn-ID",
			      "ar": "ar-Arab-EG",
			      "arc": "arc-Armi-IR",
			      "arc-Nbat": "arc-Nbat-JO",
			      "arc-Palm": "arc-Palm-SY",
			      "arn": "arn-Latn-CL",
			      "aro": "aro-Latn-BO",
			      "arq": "arq-Arab-DZ",
			      "ary": "ary-Arab-MA",
			      "arz": "arz-Arab-EG",
			      "as": "as-Beng-IN",
			      "asa": "asa-Latn-TZ",
			      "ast": "ast-Latn-ES",
			      "atj": "atj-Latn-CA",
			      "av": "av-Cyrl-RU",
			      "awa": "awa-Deva-IN",
			      "ay": "ay-Latn-BO",
			      "az": "az-Latn-AZ",
			      "az-Arab": "az-Arab-IR",
			      "az-IR": "az-Arab-IR",
			      "az-RU": "az-Cyrl-RU",
			      "azb": "azb-Arab-IR",
			      "ba": "ba-Cyrl-RU",
			      "bal": "bal-Arab-PK",
			      "ban": "ban-Latn-ID",
			      "bap": "bap-Deva-NP",
			      "bar": "bar-Latn-AT",
			      "bas": "bas-Latn-CM",
			      "bax": "bax-Bamu-CM",
			      "bbc": "bbc-Latn-ID",
			      "bbj": "bbj-Latn-CM",
			      "bci": "bci-Latn-CI",
			      "be": "be-Cyrl-BY",
			      "bem": "bem-Latn-ZM",
			      "bew": "bew-Latn-ID",
			      "bez": "bez-Latn-TZ",
			      "bfd": "bfd-Latn-CM",
			      "bfq": "bfq-Taml-IN",
			      "bft": "bft-Arab-PK",
			      "bfy": "bfy-Deva-IN",
			      "bg": "bg-Cyrl-BG",
			      "bgc": "bgc-Deva-IN",
			      "bgx": "bgx-Grek-TR",
			      "bh": "bh-Kthi-IN",
			      "bhb": "bhb-Deva-IN",
			      "bhi": "bhi-Deva-IN",
			      "bhk": "bhk-Latn-PH",
			      "bho": "bho-Deva-IN",
			      "bi": "bi-Latn-VU",
			      "bik": "bik-Latn-PH",
			      "bin": "bin-Latn-NG",
			      "bjj": "bjj-Deva-IN",
			      "bjn": "bjn-Latn-ID",
			      "bkm": "bkm-Latn-CM",
			      "bku": "bku-Latn-PH",
			      "blt": "blt-Tavt-VN",
			      "bm": "bm-Latn-ML",
			      "bmq": "bmq-Latn-ML",
			      "bn": "bn-Beng-BD",
			      "bo": "bo-Tibt-CN",
			      "bpy": "bpy-Beng-IN",
			      "bqi": "bqi-Arab-IR",
			      "bqv": "bqv-Latn-CI",
			      "br": "br-Latn-FR",
			      "bra": "bra-Deva-IN",
			      "brh": "brh-Arab-PK",
			      "brx": "brx-Deva-IN",
			      "bs": "bs-Latn-BA",
			      "bsq": "bsq-Bass-LR",
			      "bss": "bss-Latn-CM",
			      "bto": "bto-Latn-PH",
			      "btv": "btv-Deva-PK",
			      "bua": "bua-Cyrl-RU",
			      "buc": "buc-Latn-YT",
			      "bug": "bug-Latn-ID",
			      "bum": "bum-Latn-CM",
			      "bvb": "bvb-Latn-GQ",
			      "byn": "byn-Ethi-ER",
			      "byv": "byv-Latn-CM",
			      "bze": "bze-Latn-ML",
			      "ca": "ca-Latn-ES",
			      "cch": "cch-Latn-NG",
			      "ccp": "ccp-Beng-IN",
			      "ccp-Cakm": "ccp-Cakm-BD",
			      "ce": "ce-Cyrl-RU",
			      "ceb": "ceb-Latn-PH",
			      "cgg": "cgg-Latn-UG",
			      "ch": "ch-Latn-GU",
			      "chk": "chk-Latn-FM",
			      "chm": "chm-Cyrl-RU",
			      "chp": "chp-Latn-CA",
			      "chr": "chr-Cher-US",
			      "cja": "cja-Arab-KH",
			      "cjm": "cjm-Cham-VN",
			      "ckb": "ckb-Arab-IQ",
			      "co": "co-Latn-FR",
			      "cop": "cop-Copt-EG",
			      "cps": "cps-Latn-PH",
			      "cr": "cr-Cans-CA",
			      "crj": "crj-Cans-CA",
			      "crk": "crk-Cans-CA",
			      "crl": "crl-Cans-CA",
			      "crm": "crm-Cans-CA",
			      "crs": "crs-Latn-SC",
			      "cs": "cs-Latn-CZ",
			      "csb": "csb-Latn-PL",
			      "csw": "csw-Cans-CA",
			      "ctd": "ctd-Pauc-MM",
			      "cu": "cu-Cyrl-RU",
			      "cu-Glag": "cu-Glag-BG",
			      "cv": "cv-Cyrl-RU",
			      "cy": "cy-Latn-GB",
			      "da": "da-Latn-DK",
			      "dar": "dar-Cyrl-RU",
			      "dav": "dav-Latn-KE",
			      "dcc": "dcc-Arab-IN",
			      "de": "de-Latn-DE",
			      "den": "den-Latn-CA",
			      "dgr": "dgr-Latn-CA",
			      "dje": "dje-Latn-NE",
			      "dnj": "dnj-Latn-CI",
			      "doi": "doi-Arab-IN",
			      "dsb": "dsb-Latn-DE",
			      "dtm": "dtm-Latn-ML",
			      "dtp": "dtp-Latn-MY",
			      "dua": "dua-Latn-CM",
			      "dv": "dv-Thaa-MV",
			      "dyo": "dyo-Latn-SN",
			      "dyu": "dyu-Latn-BF",
			      "dz": "dz-Tibt-BT",
			      "ebu": "ebu-Latn-KE",
			      "ee": "ee-Latn-GH",
			      "efi": "efi-Latn-NG",
			      "egl": "egl-Latn-IT",
			      "egy": "egy-Egyp-EG",
			      "eky": "eky-Kali-MM",
			      "el": "el-Grek-GR",
			      "en": "en-Latn-US",
			      "en-Shaw": "en-Shaw-GB",
			      "eo": "eo-Latn-001",
			      "es": "es-Latn-ES",
			      "esu": "esu-Latn-US",
			      "et": "et-Latn-EE",
			      "ett": "ett-Ital-IT",
			      "eu": "eu-Latn-ES",
			      "ewo": "ewo-Latn-CM",
			      "ext": "ext-Latn-ES",
			      "fa": "fa-Arab-IR",
			      "fan": "fan-Latn-GQ",
			      "ff": "ff-Latn-SN",
			      "ffm": "ffm-Latn-ML",
			      "fi": "fi-Latn-FI",
			      "fil": "fil-Latn-PH",
			      "fit": "fit-Latn-SE",
			      "fj": "fj-Latn-FJ",
			      "fo": "fo-Latn-FO",
			      "fon": "fon-Latn-BJ",
			      "fr": "fr-Latn-FR",
			      "frc": "frc-Latn-US",
			      "frp": "frp-Latn-FR",
			      "frr": "frr-Latn-DE",
			      "frs": "frs-Latn-DE",
			      "fud": "fud-Latn-WF",
			      "fuq": "fuq-Latn-NE",
			      "fur": "fur-Latn-IT",
			      "fuv": "fuv-Latn-NG",
			      "fy": "fy-Latn-NL",
			      "ga": "ga-Latn-IE",
			      "gaa": "gaa-Latn-GH",
			      "gag": "gag-Latn-MD",
			      "gan": "gan-Hans-CN",
			      "gbm": "gbm-Deva-IN",
			      "gbz": "gbz-Arab-IR",
			      "gcr": "gcr-Latn-GF",
			      "gd": "gd-Latn-GB",
			      "gez": "gez-Ethi-ET",
			      "ggn": "ggn-Deva-NP",
			      "gil": "gil-Latn-KI",
			      "gjk": "gjk-Arab-PK",
			      "gju": "gju-Arab-PK",
			      "gl": "gl-Latn-ES",
			      "glk": "glk-Arab-IR",
			      "gn": "gn-Latn-PY",
			      "gom": "gom-Deva-IN",
			      "gon": "gon-Telu-IN",
			      "gor": "gor-Latn-ID",
			      "gos": "gos-Latn-NL",
			      "got": "got-Goth-UA",
			      "grc": "grc-Cprt-CY",
			      "grc-Linb": "grc-Linb-GR",
			      "grt": "grt-Beng-IN",
			      "gsw": "gsw-Latn-CH",
			      "gu": "gu-Gujr-IN",
			      "gub": "gub-Latn-BR",
			      "guc": "guc-Latn-CO",
			      "gur": "gur-Latn-GH",
			      "guz": "guz-Latn-KE",
			      "gv": "gv-Latn-IM",
			      "gvr": "gvr-Deva-NP",
			      "gwi": "gwi-Latn-CA",
			      "ha": "ha-Latn-NG",
			      "ha-CM": "ha-Arab-CM",
			      "ha-SD": "ha-Arab-SD",
			      "hak": "hak-Hans-CN",
			      "haw": "haw-Latn-US",
			      "haz": "haz-Arab-AF",
			      "he": "he-Hebr-IL",
			      "hi": "hi-Deva-IN",
			      "hif": "hif-Deva-FJ",
			      "hil": "hil-Latn-PH",
			      "hmd": "hmd-Plrd-CN",
			      "hnd": "hnd-Arab-PK",
			      "hne": "hne-Deva-IN",
			      "hnj": "hnj-Hmng-LA",
			      "hnn": "hnn-Latn-PH",
			      "hno": "hno-Arab-PK",
			      "ho": "ho-Latn-PG",
			      "hoc": "hoc-Deva-IN",
			      "hoj": "hoj-Deva-IN",
			      "hr": "hr-Latn-HR",
			      "hsb": "hsb-Latn-DE",
			      "hsn": "hsn-Hans-CN",
			      "ht": "ht-Latn-HT",
			      "hu": "hu-Latn-HU",
			      "hy": "hy-Armn-AM",
			      "ia": "ia-Latn-FR",
			      "ibb": "ibb-Latn-NG",
			      "id": "id-Latn-ID",
			      "ig": "ig-Latn-NG",
			      "ii": "ii-Yiii-CN",
			      "ik": "ik-Latn-US",
			      "ikt": "ikt-Latn-CA",
			      "ilo": "ilo-Latn-PH",
			      "in": "in-Latn-ID",
			      "inh": "inh-Cyrl-RU",
			      "is": "is-Latn-IS",
			      "it": "it-Latn-IT",
			      "iu": "iu-Cans-CA",
			      "iw": "iw-Hebr-IL",
			      "izh": "izh-Latn-RU",
			      "ja": "ja-Jpan-JP",
			      "jam": "jam-Latn-JM",
			      "jgo": "jgo-Latn-CM",
			      "ji": "ji-Hebr-UA",
			      "jmc": "jmc-Latn-TZ",
			      "jml": "jml-Deva-NP",
			      "jut": "jut-Latn-DK",
			      "jv": "jv-Latn-ID",
			      "jw": "jw-Latn-ID",
			      "ka": "ka-Geor-GE",
			      "kaa": "kaa-Cyrl-UZ",
			      "kab": "kab-Latn-DZ",
			      "kaj": "kaj-Latn-NG",
			      "kam": "kam-Latn-KE",
			      "kao": "kao-Latn-ML",
			      "kbd": "kbd-Cyrl-RU",
			      "kcg": "kcg-Latn-NG",
			      "kck": "kck-Latn-ZW",
			      "kde": "kde-Latn-TZ",
			      "kdt": "kdt-Thai-TH",
			      "kea": "kea-Latn-CV",
			      "ken": "ken-Latn-CM",
			      "kfo": "kfo-Latn-CI",
			      "kfr": "kfr-Deva-IN",
			      "kfy": "kfy-Deva-IN",
			      "kg": "kg-Latn-CD",
			      "kge": "kge-Latn-ID",
			      "kgp": "kgp-Latn-BR",
			      "kha": "kha-Latn-IN",
			      "khb": "khb-Talu-CN",
			      "khn": "khn-Deva-IN",
			      "khq": "khq-Latn-ML",
			      "kht": "kht-Mymr-IN",
			      "khw": "khw-Arab-PK",
			      "ki": "ki-Latn-KE",
			      "kiu": "kiu-Latn-TR",
			      "kj": "kj-Latn-NA",
			      "kjg": "kjg-Laoo-LA",
			      "kk": "kk-Cyrl-KZ",
			      "kk-AF": "kk-Arab-AF",
			      "kk-Arab": "kk-Arab-CN",
			      "kk-CN": "kk-Arab-CN",
			      "kk-IR": "kk-Arab-IR",
			      "kk-MN": "kk-Arab-MN",
			      "kkj": "kkj-Latn-CM",
			      "kl": "kl-Latn-GL",
			      "kln": "kln-Latn-KE",
			      "km": "km-Khmr-KH",
			      "kmb": "kmb-Latn-AO",
			      "kn": "kn-Knda-IN",
			      "ko": "ko-Kore-KR",
			      "koi": "koi-Cyrl-RU",
			      "kok": "kok-Deva-IN",
			      "kos": "kos-Latn-FM",
			      "kpe": "kpe-Latn-LR",
			      "krc": "krc-Cyrl-RU",
			      "kri": "kri-Latn-SL",
			      "krj": "krj-Latn-PH",
			      "krl": "krl-Latn-RU",
			      "kru": "kru-Deva-IN",
			      "ks": "ks-Arab-IN",
			      "ksb": "ksb-Latn-TZ",
			      "ksf": "ksf-Latn-CM",
			      "ksh": "ksh-Latn-DE",
			      "ku": "ku-Latn-TR",
			      "ku-Arab": "ku-Arab-IQ",
			      "ku-LB": "ku-Arab-LB",
			      "kum": "kum-Cyrl-RU",
			      "kv": "kv-Cyrl-RU",
			      "kvr": "kvr-Latn-ID",
			      "kvx": "kvx-Arab-PK",
			      "kw": "kw-Latn-GB",
			      "kxm": "kxm-Thai-TH",
			      "kxp": "kxp-Arab-PK",
			      "ky": "ky-Cyrl-KG",
			      "ky-Arab": "ky-Arab-CN",
			      "ky-CN": "ky-Arab-CN",
			      "ky-Latn": "ky-Latn-TR",
			      "ky-TR": "ky-Latn-TR",
			      "la": "la-Latn-VA",
			      "lab": "lab-Lina-GR",
			      "lad": "lad-Hebr-IL",
			      "lag": "lag-Latn-TZ",
			      "lah": "lah-Arab-PK",
			      "laj": "laj-Latn-UG",
			      "lb": "lb-Latn-LU",
			      "lbe": "lbe-Cyrl-RU",
			      "lbw": "lbw-Latn-ID",
			      "lcp": "lcp-Thai-CN",
			      "lep": "lep-Lepc-IN",
			      "lez": "lez-Cyrl-RU",
			      "lg": "lg-Latn-UG",
			      "li": "li-Latn-NL",
			      "lif": "lif-Deva-NP",
			      "lif-Limb": "lif-Limb-IN",
			      "lij": "lij-Latn-IT",
			      "lis": "lis-Lisu-CN",
			      "ljp": "ljp-Latn-ID",
			      "lki": "lki-Arab-IR",
			      "lkt": "lkt-Latn-US",
			      "lmn": "lmn-Telu-IN",
			      "lmo": "lmo-Latn-IT",
			      "ln": "ln-Latn-CD",
			      "lo": "lo-Laoo-LA",
			      "lol": "lol-Latn-CD",
			      "loz": "loz-Latn-ZM",
			      "lrc": "lrc-Arab-IR",
			      "lt": "lt-Latn-LT",
			      "ltg": "ltg-Latn-LV",
			      "lu": "lu-Latn-CD",
			      "lua": "lua-Latn-CD",
			      "luo": "luo-Latn-KE",
			      "luy": "luy-Latn-KE",
			      "luz": "luz-Arab-IR",
			      "lv": "lv-Latn-LV",
			      "lwl": "lwl-Thai-TH",
			      "lzh": "lzh-Hans-CN",
			      "lzz": "lzz-Latn-TR",
			      "mad": "mad-Latn-ID",
			      "maf": "maf-Latn-CM",
			      "mag": "mag-Deva-IN",
			      "mai": "mai-Deva-IN",
			      "mak": "mak-Latn-ID",
			      "man": "man-Latn-GM",
			      "man-GN": "man-Nkoo-GN",
			      "man-Nkoo": "man-Nkoo-GN",
			      "mas": "mas-Latn-KE",
			      "maz": "maz-Latn-MX",
			      "mdf": "mdf-Cyrl-RU",
			      "mdh": "mdh-Latn-PH",
			      "mdr": "mdr-Latn-ID",
			      "men": "men-Latn-SL",
			      "mer": "mer-Latn-KE",
			      "mfa": "mfa-Arab-TH",
			      "mfe": "mfe-Latn-MU",
			      "mg": "mg-Latn-MG",
			      "mgh": "mgh-Latn-MZ",
			      "mgo": "mgo-Latn-CM",
			      "mgp": "mgp-Deva-NP",
			      "mgy": "mgy-Latn-TZ",
			      "mh": "mh-Latn-MH",
			      "mi": "mi-Latn-NZ",
			      "min": "min-Latn-ID",
			      "mk": "mk-Cyrl-MK",
			      "ml": "ml-Mlym-IN",
			      "mn": "mn-Cyrl-MN",
			      "mn-CN": "mn-Mong-CN",
			      "mn-Mong": "mn-Mong-CN",
			      "mni": "mni-Beng-IN",
			      "mnw": "mnw-Mymr-MM",
			      "moe": "moe-Latn-CA",
			      "moh": "moh-Latn-CA",
			      "mos": "mos-Latn-BF",
			      "mr": "mr-Deva-IN",
			      "mrd": "mrd-Deva-NP",
			      "mrj": "mrj-Cyrl-RU",
			      "mru": "mru-Mroo-BD",
			      "ms": "ms-Latn-MY",
			      "ms-CC": "ms-Arab-CC",
			      "ms-ID": "ms-Arab-ID",
			      "mt": "mt-Latn-MT",
			      "mtr": "mtr-Deva-IN",
			      "mua": "mua-Latn-CM",
			      "mvy": "mvy-Arab-PK",
			      "mwk": "mwk-Latn-ML",
			      "mwr": "mwr-Deva-IN",
			      "mwv": "mwv-Latn-ID",
			      "mxc": "mxc-Latn-ZW",
			      "my": "my-Mymr-MM",
			      "myv": "myv-Cyrl-RU",
			      "myx": "myx-Latn-UG",
			      "myz": "myz-Mand-IR",
			      "mzn": "mzn-Arab-IR",
			      "na": "na-Latn-NR",
			      "nan": "nan-Hans-CN",
			      "nap": "nap-Latn-IT",
			      "naq": "naq-Latn-NA",
			      "nb": "nb-Latn-NO",
			      "nch": "nch-Latn-MX",
			      "nd": "nd-Latn-ZW",
			      "ndc": "ndc-Latn-MZ",
			      "nds": "nds-Latn-DE",
			      "ne": "ne-Deva-NP",
			      "new": "new-Deva-NP",
			      "ng": "ng-Latn-NA",
			      "ngl": "ngl-Latn-MZ",
			      "nhe": "nhe-Latn-MX",
			      "nhw": "nhw-Latn-MX",
			      "nij": "nij-Latn-ID",
			      "niu": "niu-Latn-NU",
			      "njo": "njo-Latn-IN",
			      "nl": "nl-Latn-NL",
			      "nmg": "nmg-Latn-CM",
			      "nn": "nn-Latn-NO",
			      "nnh": "nnh-Latn-CM",
			      "no": "no-Latn-NO",
			      "nod": "nod-Lana-TH",
			      "noe": "noe-Deva-IN",
			      "non": "non-Runr-SE",
			      "nqo": "nqo-Nkoo-GN",
			      "nr": "nr-Latn-ZA",
			      "nsk": "nsk-Cans-CA",
			      "nso": "nso-Latn-ZA",
			      "nus": "nus-Latn-SD",
			      "nv": "nv-Latn-US",
			      "nxq": "nxq-Latn-CN",
			      "ny": "ny-Latn-MW",
			      "nym": "nym-Latn-TZ",
			      "nyn": "nyn-Latn-UG",
			      "oc": "oc-Latn-FR",
			      "om": "om-Latn-ET",
			      "or": "or-Orya-IN",
			      "os": "os-Cyrl-GE",
			      "otk": "otk-Orkh-MN",
			      "pa": "pa-Guru-IN",
			      "pa-Arab": "pa-Arab-PK",
			      "pa-PK": "pa-Arab-PK",
			      "pag": "pag-Latn-PH",
			      "pal": "pal-Phli-IR",
			      "pal-Phlp": "pal-Phlp-CN",
			      "pam": "pam-Latn-PH",
			      "pap": "pap-Latn-AW",
			      "pau": "pau-Latn-PW",
			      "pcd": "pcd-Latn-FR",
			      "pcm": "pcm-Latn-NG",
			      "pdc": "pdc-Latn-US",
			      "pdt": "pdt-Latn-CA",
			      "peo": "peo-Xpeo-IR",
			      "pfl": "pfl-Latn-DE",
			      "phn": "phn-Phnx-LB",
			      "pka": "pka-Brah-IN",
			      "pko": "pko-Latn-KE",
			      "pl": "pl-Latn-PL",
			      "pms": "pms-Latn-IT",
			      "pnt": "pnt-Grek-GR",
			      "pon": "pon-Latn-FM",
			      "pra": "pra-Khar-PK",
			      "prd": "prd-Arab-IR",
			      "prg": "prg-Latn-001",
			      "ps": "ps-Arab-AF",
			      "pt": "pt-Latn-BR",
			      "puu": "puu-Latn-GA",
			      "qu": "qu-Latn-PE",
			      "quc": "quc-Latn-GT",
			      "qug": "qug-Latn-EC",
			      "raj": "raj-Latn-IN",
			      "rcf": "rcf-Latn-RE",
			      "rej": "rej-Latn-ID",
			      "rgn": "rgn-Latn-IT",
			      "ria": "ria-Latn-IN",
			      "rif": "rif-Tfng-MA",
			      "rif-NL": "rif-Latn-NL",
			      "rjs": "rjs-Deva-NP",
			      "rkt": "rkt-Beng-BD",
			      "rm": "rm-Latn-CH",
			      "rmf": "rmf-Latn-FI",
			      "rmo": "rmo-Latn-CH",
			      "rmt": "rmt-Arab-IR",
			      "rmu": "rmu-Latn-SE",
			      "rn": "rn-Latn-BI",
			      "rng": "rng-Latn-MZ",
			      "ro": "ro-Latn-RO",
			      "rob": "rob-Latn-ID",
			      "rof": "rof-Latn-TZ",
			      "rtm": "rtm-Latn-FJ",
			      "ru": "ru-Cyrl-RU",
			      "rue": "rue-Cyrl-UA",
			      "rug": "rug-Latn-SB",
			      "rw": "rw-Latn-RW",
			      "rwk": "rwk-Latn-TZ",
			      "ryu": "ryu-Kana-JP",
			      "sa": "sa-Deva-IN",
			      "saf": "saf-Latn-GH",
			      "sah": "sah-Cyrl-RU",
			      "saq": "saq-Latn-KE",
			      "sas": "sas-Latn-ID",
			      "sat": "sat-Latn-IN",
			      "saz": "saz-Saur-IN",
			      "sbp": "sbp-Latn-TZ",
			      "sc": "sc-Latn-IT",
			      "sck": "sck-Deva-IN",
			      "scn": "scn-Latn-IT",
			      "sco": "sco-Latn-GB",
			      "scs": "scs-Latn-CA",
			      "sd": "sd-Arab-PK",
			      "sd-Deva": "sd-Deva-IN",
			      "sd-Khoj": "sd-Khoj-IN",
			      "sd-Sind": "sd-Sind-IN",
			      "sdc": "sdc-Latn-IT",
			      "se": "se-Latn-NO",
			      "sef": "sef-Latn-CI",
			      "seh": "seh-Latn-MZ",
			      "sei": "sei-Latn-MX",
			      "ses": "ses-Latn-ML",
			      "sg": "sg-Latn-CF",
			      "sga": "sga-Ogam-IE",
			      "sgs": "sgs-Latn-LT",
			      "shi": "shi-Tfng-MA",
			      "shn": "shn-Mymr-MM",
			      "si": "si-Sinh-LK",
			      "sid": "sid-Latn-ET",
			      "sk": "sk-Latn-SK",
			      "skr": "skr-Arab-PK",
			      "sl": "sl-Latn-SI",
			      "sli": "sli-Latn-PL",
			      "sly": "sly-Latn-ID",
			      "sm": "sm-Latn-WS",
			      "sma": "sma-Latn-SE",
			      "smj": "smj-Latn-SE",
			      "smn": "smn-Latn-FI",
			      "smp": "smp-Samr-IL",
			      "sms": "sms-Latn-FI",
			      "sn": "sn-Latn-ZW",
			      "snk": "snk-Latn-ML",
			      "so": "so-Latn-SO",
			      "sou": "sou-Thai-TH",
			      "sq": "sq-Latn-AL",
			      "sr": "sr-Cyrl-RS",
			      "sr-ME": "sr-Latn-ME",
			      "sr-RO": "sr-Latn-RO",
			      "sr-RU": "sr-Latn-RU",
			      "sr-TR": "sr-Latn-TR",
			      "srb": "srb-Sora-IN",
			      "srn": "srn-Latn-SR",
			      "srr": "srr-Latn-SN",
			      "srx": "srx-Deva-IN",
			      "ss": "ss-Latn-ZA",
			      "ssy": "ssy-Latn-ER",
			      "st": "st-Latn-ZA",
			      "stq": "stq-Latn-DE",
			      "su": "su-Latn-ID",
			      "suk": "suk-Latn-TZ",
			      "sus": "sus-Latn-GN",
			      "sv": "sv-Latn-SE",
			      "sw": "sw-Latn-TZ",
			      "swb": "swb-Arab-YT",
			      "swc": "swc-Latn-CD",
			      "swv": "swv-Deva-IN",
			      "sxn": "sxn-Latn-ID",
			      "syl": "syl-Beng-BD",
			      "syr": "syr-Syrc-IQ",
			      "szl": "szl-Latn-PL",
			      "ta": "ta-Taml-IN",
			      "taj": "taj-Deva-NP",
			      "tbw": "tbw-Latn-PH",
			      "tcy": "tcy-Knda-IN",
			      "tdd": "tdd-Tale-CN",
			      "tdg": "tdg-Deva-NP",
			      "tdh": "tdh-Deva-NP",
			      "te": "te-Telu-IN",
			      "tem": "tem-Latn-SL",
			      "teo": "teo-Latn-UG",
			      "tet": "tet-Latn-TL",
			      "tg": "tg-Cyrl-TJ",
			      "tg-Arab": "tg-Arab-PK",
			      "tg-PK": "tg-Arab-PK",
			      "th": "th-Thai-TH",
			      "thl": "thl-Deva-NP",
			      "thq": "thq-Deva-NP",
			      "thr": "thr-Deva-NP",
			      "ti": "ti-Ethi-ET",
			      "tig": "tig-Ethi-ER",
			      "tiv": "tiv-Latn-NG",
			      "tk": "tk-Latn-TM",
			      "tkl": "tkl-Latn-TK",
			      "tkr": "tkr-Latn-AZ",
			      "tkt": "tkt-Deva-NP",
			      "tl": "tl-Latn-PH",
			      "tly": "tly-Latn-AZ",
			      "tmh": "tmh-Latn-NE",
			      "tn": "tn-Latn-ZA",
			      "to": "to-Latn-TO",
			      "tpi": "tpi-Latn-PG",
			      "tr": "tr-Latn-TR",
			      "tru": "tru-Latn-TR",
			      "trv": "trv-Latn-TW",
			      "ts": "ts-Latn-ZA",
			      "tsd": "tsd-Grek-GR",
			      "tsf": "tsf-Deva-NP",
			      "tsg": "tsg-Latn-PH",
			      "tsj": "tsj-Tibt-BT",
			      "tt": "tt-Cyrl-RU",
			      "ttj": "ttj-Latn-UG",
			      "tts": "tts-Thai-TH",
			      "ttt": "ttt-Latn-AZ",
			      "tum": "tum-Latn-MW",
			      "tvl": "tvl-Latn-TV",
			      "twq": "twq-Latn-NE",
			      "ty": "ty-Latn-PF",
			      "tyv": "tyv-Cyrl-RU",
			      "tzm": "tzm-Latn-MA",
			      "udm": "udm-Cyrl-RU",
			      "ug": "ug-Arab-CN",
			      "ug-Cyrl": "ug-Cyrl-KZ",
			      "ug-KZ": "ug-Cyrl-KZ",
			      "ug-MN": "ug-Cyrl-MN",
			      "uga": "uga-Ugar-SY",
			      "uk": "uk-Cyrl-UA",
			      "uli": "uli-Latn-FM",
			      "umb": "umb-Latn-AO",
			      "und": "en-Latn-US",
			      "und-002": "en-Latn-NG",
			      "und-003": "en-Latn-US",
			      "und-005": "pt-Latn-BR",
			      "und-009": "en-Latn-AU",
			      "und-011": "en-Latn-NG",
			      "und-013": "es-Latn-MX",
			      "und-014": "en-Latn-KE",
			      "und-015": "ar-Arab-EG",
			      "und-017": "sw-Latn-CD",
			      "und-018": "en-Latn-ZA",
			      "und-019": "en-Latn-US",
			      "und-021": "en-Latn-US",
			      "und-029": "es-Latn-CU",
			      "und-030": "zh-Hans-CN",
			      "und-034": "hi-Deva-IN",
			      "und-035": "id-Latn-ID",
			      "und-039": "it-Latn-IT",
			      "und-053": "en-Latn-AU",
			      "und-054": "en-Latn-PG",
			      "und-057": "en-Latn-KI",
			      "und-061": "sm-Latn-WS",
			      "und-142": "zh-Hans-CN",
			      "und-143": "uz-Latn-UZ",
			      "und-145": "ar-Arab-SA",
			      "und-150": "ru-Cyrl-RU",
			      "und-151": "ru-Cyrl-RU",
			      "und-154": "en-Latn-GB",
			      "und-155": "de-Latn-DE",
			      "und-419": "es-Latn-419",
			      "und-AD": "ca-Latn-AD",
			      "und-AE": "ar-Arab-AE",
			      "und-AF": "fa-Arab-AF",
			      "und-AL": "sq-Latn-AL",
			      "und-AM": "hy-Armn-AM",
			      "und-AO": "pt-Latn-AO",
			      "und-AQ": "und-Latn-AQ",
			      "und-AR": "es-Latn-AR",
			      "und-AS": "sm-Latn-AS",
			      "und-AT": "de-Latn-AT",
			      "und-AW": "nl-Latn-AW",
			      "und-AX": "sv-Latn-AX",
			      "und-AZ": "az-Latn-AZ",
			      "und-Aghb": "lez-Aghb-RU",
			      "und-Arab": "ar-Arab-EG",
			      "und-Arab-CC": "ms-Arab-CC",
			      "und-Arab-CN": "ug-Arab-CN",
			      "und-Arab-GB": "ks-Arab-GB",
			      "und-Arab-ID": "ms-Arab-ID",
			      "und-Arab-IN": "ur-Arab-IN",
			      "und-Arab-KH": "cja-Arab-KH",
			      "und-Arab-MN": "kk-Arab-MN",
			      "und-Arab-MU": "ur-Arab-MU",
			      "und-Arab-NG": "ha-Arab-NG",
			      "und-Arab-PK": "ur-Arab-PK",
			      "und-Arab-TH": "mfa-Arab-TH",
			      "und-Arab-TJ": "fa-Arab-TJ",
			      "und-Arab-YT": "swb-Arab-YT",
			      "und-Armi": "arc-Armi-IR",
			      "und-Armn": "hy-Armn-AM",
			      "und-Avst": "ae-Avst-IR",
			      "und-BA": "bs-Latn-BA",
			      "und-BD": "bn-Beng-BD",
			      "und-BE": "nl-Latn-BE",
			      "und-BF": "fr-Latn-BF",
			      "und-BG": "bg-Cyrl-BG",
			      "und-BH": "ar-Arab-BH",
			      "und-BI": "rn-Latn-BI",
			      "und-BJ": "fr-Latn-BJ",
			      "und-BL": "fr-Latn-BL",
			      "und-BN": "ms-Latn-BN",
			      "und-BO": "es-Latn-BO",
			      "und-BQ": "pap-Latn-BQ",
			      "und-BR": "pt-Latn-BR",
			      "und-BT": "dz-Tibt-BT",
			      "und-BV": "und-Latn-BV",
			      "und-BY": "be-Cyrl-BY",
			      "und-Bali": "ban-Bali-ID",
			      "und-Bamu": "bax-Bamu-CM",
			      "und-Bass": "bsq-Bass-LR",
			      "und-Batk": "bbc-Batk-ID",
			      "und-Beng": "bn-Beng-BD",
			      "und-Bopo": "zh-Bopo-TW",
			      "und-Brah": "pka-Brah-IN",
			      "und-Brai": "fr-Brai-FR",
			      "und-Bugi": "bug-Bugi-ID",
			      "und-Buhd": "bku-Buhd-PH",
			      "und-CD": "sw-Latn-CD",
			      "und-CF": "fr-Latn-CF",
			      "und-CG": "fr-Latn-CG",
			      "und-CH": "de-Latn-CH",
			      "und-CI": "fr-Latn-CI",
			      "und-CL": "es-Latn-CL",
			      "und-CM": "fr-Latn-CM",
			      "und-CN": "zh-Hans-CN",
			      "und-CO": "es-Latn-CO",
			      "und-CP": "und-Latn-CP",
			      "und-CR": "es-Latn-CR",
			      "und-CU": "es-Latn-CU",
			      "und-CV": "pt-Latn-CV",
			      "und-CW": "pap-Latn-CW",
			      "und-CY": "el-Grek-CY",
			      "und-CZ": "cs-Latn-CZ",
			      "und-Cakm": "ccp-Cakm-BD",
			      "und-Cans": "cr-Cans-CA",
			      "und-Cari": "xcr-Cari-TR",
			      "und-Cham": "cjm-Cham-VN",
			      "und-Cher": "chr-Cher-US",
			      "und-Copt": "cop-Copt-EG",
			      "und-Cprt": "grc-Cprt-CY",
			      "und-Cyrl": "ru-Cyrl-RU",
			      "und-Cyrl-AL": "mk-Cyrl-AL",
			      "und-Cyrl-BA": "sr-Cyrl-BA",
			      "und-Cyrl-GE": "ab-Cyrl-GE",
			      "und-Cyrl-GR": "mk-Cyrl-GR",
			      "und-Cyrl-MD": "uk-Cyrl-MD",
			      "und-Cyrl-PL": "be-Cyrl-PL",
			      "und-Cyrl-RO": "bg-Cyrl-RO",
			      "und-Cyrl-SK": "uk-Cyrl-SK",
			      "und-Cyrl-TR": "kbd-Cyrl-TR",
			      "und-Cyrl-XK": "sr-Cyrl-XK",
			      "und-DE": "de-Latn-DE",
			      "und-DJ": "aa-Latn-DJ",
			      "und-DK": "da-Latn-DK",
			      "und-DO": "es-Latn-DO",
			      "und-DZ": "ar-Arab-DZ",
			      "und-Deva": "hi-Deva-IN",
			      "und-Deva-BT": "ne-Deva-BT",
			      "und-Deva-FJ": "hif-Deva-FJ",
			      "und-Deva-MU": "bho-Deva-MU",
			      "und-Deva-PK": "btv-Deva-PK",
			      "und-Dupl": "fr-Dupl-FR",
			      "und-EA": "es-Latn-EA",
			      "und-EC": "es-Latn-EC",
			      "und-EE": "et-Latn-EE",
			      "und-EG": "ar-Arab-EG",
			      "und-EH": "ar-Arab-EH",
			      "und-ER": "ti-Ethi-ER",
			      "und-ES": "es-Latn-ES",
			      "und-ET": "am-Ethi-ET",
			      "und-EU": "en-Latn-GB",
			      "und-Egyp": "egy-Egyp-EG",
			      "und-Elba": "sq-Elba-AL",
			      "und-Ethi": "am-Ethi-ET",
			      "und-FI": "fi-Latn-FI",
			      "und-FM": "chk-Latn-FM",
			      "und-FO": "fo-Latn-FO",
			      "und-FR": "fr-Latn-FR",
			      "und-GA": "fr-Latn-GA",
			      "und-GE": "ka-Geor-GE",
			      "und-GF": "fr-Latn-GF",
			      "und-GH": "ak-Latn-GH",
			      "und-GL": "kl-Latn-GL",
			      "und-GN": "fr-Latn-GN",
			      "und-GP": "fr-Latn-GP",
			      "und-GQ": "es-Latn-GQ",
			      "und-GR": "el-Grek-GR",
			      "und-GS": "und-Latn-GS",
			      "und-GT": "es-Latn-GT",
			      "und-GW": "pt-Latn-GW",
			      "und-Geor": "ka-Geor-GE",
			      "und-Glag": "cu-Glag-BG",
			      "und-Goth": "got-Goth-UA",
			      "und-Gran": "sa-Gran-IN",
			      "und-Grek": "el-Grek-GR",
			      "und-Grek-TR": "bgx-Grek-TR",
			      "und-Gujr": "gu-Gujr-IN",
			      "und-Guru": "pa-Guru-IN",
			      "und-HK": "zh-Hant-HK",
			      "und-HM": "und-Latn-HM",
			      "und-HN": "es-Latn-HN",
			      "und-HR": "hr-Latn-HR",
			      "und-HT": "ht-Latn-HT",
			      "und-HU": "hu-Latn-HU",
			      "und-Hang": "ko-Hang-KR",
			      "und-Hani": "zh-Hani-CN",
			      "und-Hano": "hnn-Hano-PH",
			      "und-Hans": "zh-Hans-CN",
			      "und-Hant": "zh-Hant-TW",
			      "und-Hebr": "he-Hebr-IL",
			      "und-Hebr-CA": "yi-Hebr-CA",
			      "und-Hebr-GB": "yi-Hebr-GB",
			      "und-Hebr-SE": "yi-Hebr-SE",
			      "und-Hebr-UA": "yi-Hebr-UA",
			      "und-Hebr-US": "yi-Hebr-US",
			      "und-Hira": "ja-Hira-JP",
			      "und-Hmng": "hnj-Hmng-LA",
			      "und-IC": "es-Latn-IC",
			      "und-ID": "id-Latn-ID",
			      "und-IL": "he-Hebr-IL",
			      "und-IN": "hi-Deva-IN",
			      "und-IQ": "ar-Arab-IQ",
			      "und-IR": "fa-Arab-IR",
			      "und-IS": "is-Latn-IS",
			      "und-IT": "it-Latn-IT",
			      "und-Ital": "ett-Ital-IT",
			      "und-JO": "ar-Arab-JO",
			      "und-JP": "ja-Jpan-JP",
			      "und-Java": "jv-Java-ID",
			      "und-Jpan": "ja-Jpan-JP",
			      "und-KG": "ky-Cyrl-KG",
			      "und-KH": "km-Khmr-KH",
			      "und-KM": "ar-Arab-KM",
			      "und-KP": "ko-Kore-KP",
			      "und-KR": "ko-Kore-KR",
			      "und-KW": "ar-Arab-KW",
			      "und-KZ": "ru-Cyrl-KZ",
			      "und-Kali": "eky-Kali-MM",
			      "und-Kana": "ja-Kana-JP",
			      "und-Khar": "pra-Khar-PK",
			      "und-Khmr": "km-Khmr-KH",
			      "und-Khoj": "sd-Khoj-IN",
			      "und-Knda": "kn-Knda-IN",
			      "und-Kore": "ko-Kore-KR",
			      "und-Kthi": "bh-Kthi-IN",
			      "und-LA": "lo-Laoo-LA",
			      "und-LB": "ar-Arab-LB",
			      "und-LI": "de-Latn-LI",
			      "und-LK": "si-Sinh-LK",
			      "und-LS": "st-Latn-LS",
			      "und-LT": "lt-Latn-LT",
			      "und-LU": "fr-Latn-LU",
			      "und-LV": "lv-Latn-LV",
			      "und-LY": "ar-Arab-LY",
			      "und-Lana": "nod-Lana-TH",
			      "und-Laoo": "lo-Laoo-LA",
			      "und-Latn-AF": "tk-Latn-AF",
			      "und-Latn-AM": "ku-Latn-AM",
			      "und-Latn-BG": "tr-Latn-BG",
			      "und-Latn-CN": "za-Latn-CN",
			      "und-Latn-CY": "tr-Latn-CY",
			      "und-Latn-DZ": "fr-Latn-DZ",
			      "und-Latn-ET": "en-Latn-ET",
			      "und-Latn-GE": "ku-Latn-GE",
			      "und-Latn-GR": "tr-Latn-GR",
			      "und-Latn-IL": "ro-Latn-IL",
			      "und-Latn-IR": "tk-Latn-IR",
			      "und-Latn-KM": "fr-Latn-KM",
			      "und-Latn-KZ": "de-Latn-KZ",
			      "und-Latn-LB": "fr-Latn-LB",
			      "und-Latn-MA": "fr-Latn-MA",
			      "und-Latn-MK": "sq-Latn-MK",
			      "und-Latn-MO": "pt-Latn-MO",
			      "und-Latn-MR": "fr-Latn-MR",
			      "und-Latn-RU": "krl-Latn-RU",
			      "und-Latn-SY": "fr-Latn-SY",
			      "und-Latn-TN": "fr-Latn-TN",
			      "und-Latn-TW": "trv-Latn-TW",
			      "und-Latn-UA": "pl-Latn-UA",
			      "und-Lepc": "lep-Lepc-IN",
			      "und-Limb": "lif-Limb-IN",
			      "und-Lina": "lab-Lina-GR",
			      "und-Linb": "grc-Linb-GR",
			      "und-Lisu": "lis-Lisu-CN",
			      "und-Lyci": "xlc-Lyci-TR",
			      "und-Lydi": "xld-Lydi-TR",
			      "und-MA": "ar-Arab-MA",
			      "und-MC": "fr-Latn-MC",
			      "und-MD": "ro-Latn-MD",
			      "und-ME": "sr-Latn-ME",
			      "und-MF": "fr-Latn-MF",
			      "und-MG": "mg-Latn-MG",
			      "und-MK": "mk-Cyrl-MK",
			      "und-ML": "bm-Latn-ML",
			      "und-MM": "my-Mymr-MM",
			      "und-MN": "mn-Cyrl-MN",
			      "und-MO": "zh-Hant-MO",
			      "und-MQ": "fr-Latn-MQ",
			      "und-MR": "ar-Arab-MR",
			      "und-MT": "mt-Latn-MT",
			      "und-MU": "mfe-Latn-MU",
			      "und-MV": "dv-Thaa-MV",
			      "und-MX": "es-Latn-MX",
			      "und-MY": "ms-Latn-MY",
			      "und-MZ": "pt-Latn-MZ",
			      "und-Mahj": "hi-Mahj-IN",
			      "und-Mand": "myz-Mand-IR",
			      "und-Mani": "xmn-Mani-CN",
			      "und-Mend": "men-Mend-SL",
			      "und-Merc": "xmr-Merc-SD",
			      "und-Mero": "xmr-Mero-SD",
			      "und-Mlym": "ml-Mlym-IN",
			      "und-Modi": "mr-Modi-IN",
			      "und-Mong": "mn-Mong-CN",
			      "und-Mroo": "mru-Mroo-BD",
			      "und-Mtei": "mni-Mtei-IN",
			      "und-Mymr": "my-Mymr-MM",
			      "und-Mymr-IN": "kht-Mymr-IN",
			      "und-Mymr-TH": "mnw-Mymr-TH",
			      "und-NA": "af-Latn-NA",
			      "und-NC": "fr-Latn-NC",
			      "und-NE": "ha-Latn-NE",
			      "und-NI": "es-Latn-NI",
			      "und-NL": "nl-Latn-NL",
			      "und-NO": "nb-Latn-NO",
			      "und-NP": "ne-Deva-NP",
			      "und-Narb": "xna-Narb-SA",
			      "und-Nbat": "arc-Nbat-JO",
			      "und-Nkoo": "man-Nkoo-GN",
			      "und-OM": "ar-Arab-OM",
			      "und-Ogam": "sga-Ogam-IE",
			      "und-Olck": "sat-Olck-IN",
			      "und-Orkh": "otk-Orkh-MN",
			      "und-Orya": "or-Orya-IN",
			      "und-Osma": "so-Osma-SO",
			      "und-PA": "es-Latn-PA",
			      "und-PE": "es-Latn-PE",
			      "und-PF": "fr-Latn-PF",
			      "und-PG": "tpi-Latn-PG",
			      "und-PH": "fil-Latn-PH",
			      "und-PK": "ur-Arab-PK",
			      "und-PL": "pl-Latn-PL",
			      "und-PM": "fr-Latn-PM",
			      "und-PR": "es-Latn-PR",
			      "und-PS": "ar-Arab-PS",
			      "und-PT": "pt-Latn-PT",
			      "und-PW": "pau-Latn-PW",
			      "und-PY": "gn-Latn-PY",
			      "und-Palm": "arc-Palm-SY",
			      "und-Pauc": "ctd-Pauc-MM",
			      "und-Perm": "kv-Perm-RU",
			      "und-Phag": "lzh-Phag-CN",
			      "und-Phli": "pal-Phli-IR",
			      "und-Phlp": "pal-Phlp-CN",
			      "und-Phnx": "phn-Phnx-LB",
			      "und-Plrd": "hmd-Plrd-CN",
			      "und-Prti": "xpr-Prti-IR",
			      "und-QA": "ar-Arab-QA",
			      "und-QO": "en-Latn-IO",
			      "und-RE": "fr-Latn-RE",
			      "und-RO": "ro-Latn-RO",
			      "und-RS": "sr-Cyrl-RS",
			      "und-RU": "ru-Cyrl-RU",
			      "und-RW": "rw-Latn-RW",
			      "und-Rjng": "rej-Rjng-ID",
			      "und-Runr": "non-Runr-SE",
			      "und-SA": "ar-Arab-SA",
			      "und-SC": "fr-Latn-SC",
			      "und-SD": "ar-Arab-SD",
			      "und-SE": "sv-Latn-SE",
			      "und-SI": "sl-Latn-SI",
			      "und-SJ": "nb-Latn-SJ",
			      "und-SK": "sk-Latn-SK",
			      "und-SM": "it-Latn-SM",
			      "und-SN": "fr-Latn-SN",
			      "und-SO": "so-Latn-SO",
			      "und-SR": "nl-Latn-SR",
			      "und-ST": "pt-Latn-ST",
			      "und-SV": "es-Latn-SV",
			      "und-SY": "ar-Arab-SY",
			      "und-Samr": "smp-Samr-IL",
			      "und-Sarb": "xsa-Sarb-YE",
			      "und-Saur": "saz-Saur-IN",
			      "und-Shaw": "en-Shaw-GB",
			      "und-Shrd": "sa-Shrd-IN",
			      "und-Sidd": "sa-Sidd-IN",
			      "und-Sind": "sd-Sind-IN",
			      "und-Sinh": "si-Sinh-LK",
			      "und-Sora": "srb-Sora-IN",
			      "und-Sund": "su-Sund-ID",
			      "und-Sylo": "syl-Sylo-BD",
			      "und-Syrc": "syr-Syrc-IQ",
			      "und-TD": "fr-Latn-TD",
			      "und-TF": "fr-Latn-TF",
			      "und-TG": "fr-Latn-TG",
			      "und-TH": "th-Thai-TH",
			      "und-TJ": "tg-Cyrl-TJ",
			      "und-TK": "tkl-Latn-TK",
			      "und-TL": "pt-Latn-TL",
			      "und-TM": "tk-Latn-TM",
			      "und-TN": "ar-Arab-TN",
			      "und-TO": "to-Latn-TO",
			      "und-TR": "tr-Latn-TR",
			      "und-TV": "tvl-Latn-TV",
			      "und-TW": "zh-Hant-TW",
			      "und-TZ": "sw-Latn-TZ",
			      "und-Tagb": "tbw-Tagb-PH",
			      "und-Takr": "doi-Takr-IN",
			      "und-Tale": "tdd-Tale-CN",
			      "und-Talu": "khb-Talu-CN",
			      "und-Taml": "ta-Taml-IN",
			      "und-Tavt": "blt-Tavt-VN",
			      "und-Telu": "te-Telu-IN",
			      "und-Tfng": "zgh-Tfng-MA",
			      "und-Tglg": "fil-Tglg-PH",
			      "und-Thaa": "dv-Thaa-MV",
			      "und-Thai": "th-Thai-TH",
			      "und-Thai-CN": "lcp-Thai-CN",
			      "und-Thai-KH": "kdt-Thai-KH",
			      "und-Thai-LA": "kdt-Thai-LA",
			      "und-Tibt": "bo-Tibt-CN",
			      "und-Tirh": "mai-Tirh-IN",
			      "und-UA": "uk-Cyrl-UA",
			      "und-UG": "sw-Latn-UG",
			      "und-UY": "es-Latn-UY",
			      "und-UZ": "uz-Latn-UZ",
			      "und-Ugar": "uga-Ugar-SY",
			      "und-VA": "it-Latn-VA",
			      "und-VE": "es-Latn-VE",
			      "und-VN": "vi-Latn-VN",
			      "und-VU": "bi-Latn-VU",
			      "und-Vaii": "vai-Vaii-LR",
			      "und-WF": "fr-Latn-WF",
			      "und-WS": "sm-Latn-WS",
			      "und-Wara": "hoc-Wara-IN",
			      "und-XK": "sq-Latn-XK",
			      "und-Xpeo": "peo-Xpeo-IR",
			      "und-Xsux": "akk-Xsux-IQ",
			      "und-YE": "ar-Arab-YE",
			      "und-YT": "fr-Latn-YT",
			      "und-Yiii": "ii-Yiii-CN",
			      "unr": "unr-Beng-IN",
			      "unr-Deva": "unr-Deva-NP",
			      "unr-NP": "unr-Deva-NP",
			      "unx": "unx-Beng-IN",
			      "ur": "ur-Arab-PK",
			      "uz": "uz-Latn-UZ",
			      "uz-AF": "uz-Arab-AF",
			      "uz-Arab": "uz-Arab-AF",
			      "uz-CN": "uz-Cyrl-CN",
			      "vai": "vai-Vaii-LR",
			      "ve": "ve-Latn-ZA",
			      "vec": "vec-Latn-IT",
			      "vep": "vep-Latn-RU",
			      "vi": "vi-Latn-VN",
			      "vic": "vic-Latn-SX",
			      "vls": "vls-Latn-BE",
			      "vmf": "vmf-Latn-DE",
			      "vmw": "vmw-Latn-MZ",
			      "vo": "vo-Latn-001",
			      "vro": "vro-Latn-EE",
			      "vun": "vun-Latn-TZ",
			      "wa": "wa-Latn-BE",
			      "wae": "wae-Latn-CH",
			      "wal": "wal-Ethi-ET",
			      "war": "war-Latn-PH",
			      "wbq": "wbq-Telu-IN",
			      "wbr": "wbr-Deva-IN",
			      "wls": "wls-Latn-WF",
			      "wo": "wo-Latn-SN",
			      "wtm": "wtm-Deva-IN",
			      "wuu": "wuu-Hans-CN",
			      "xav": "xav-Latn-BR",
			      "xcr": "xcr-Cari-TR",
			      "xh": "xh-Latn-ZA",
			      "xlc": "xlc-Lyci-TR",
			      "xld": "xld-Lydi-TR",
			      "xmf": "xmf-Geor-GE",
			      "xmn": "xmn-Mani-CN",
			      "xmr": "xmr-Merc-SD",
			      "xna": "xna-Narb-SA",
			      "xnr": "xnr-Deva-IN",
			      "xog": "xog-Latn-UG",
			      "xpr": "xpr-Prti-IR",
			      "xsa": "xsa-Sarb-YE",
			      "xsr": "xsr-Deva-NP",
			      "yao": "yao-Latn-MZ",
			      "yap": "yap-Latn-FM",
			      "yav": "yav-Latn-CM",
			      "ybb": "ybb-Latn-CM",
			      "yi": "yi-Hebr-001",
			      "yo": "yo-Latn-NG",
			      "yrl": "yrl-Latn-BR",
			      "yua": "yua-Latn-MX",
			      "za": "za-Latn-CN",
			      "zdj": "zdj-Arab-KM",
			      "zea": "zea-Latn-NL",
			      "zgh": "zgh-Tfng-MA",
			      "zh": "zh-Hans-CN",
			      "zh-AU": "zh-Hant-AU",
			      "zh-BN": "zh-Hant-BN",
			      "zh-Bopo": "zh-Bopo-TW",
			      "zh-GB": "zh-Hant-GB",
			      "zh-GF": "zh-Hant-GF",
			      "zh-HK": "zh-Hant-HK",
			      "zh-Hant": "zh-Hant-TW",
			      "zh-ID": "zh-Hant-ID",
			      "zh-MO": "zh-Hant-MO",
			      "zh-MY": "zh-Hant-MY",
			      "zh-PA": "zh-Hant-PA",
			      "zh-PF": "zh-Hant-PF",
			      "zh-PH": "zh-Hant-PH",
			      "zh-SR": "zh-Hant-SR",
			      "zh-TH": "zh-Hant-TH",
			      "zh-TW": "zh-Hant-TW",
			      "zh-US": "zh-Hant-US",
			      "zh-VN": "zh-Hant-VN",
			      "zmi": "zmi-Latn-MY",
			      "zu": "zu-Latn-ZA",
			      "zza": "zza-Latn-TR"
			    }
			  }
			};

var fakeSupplementalNumberingSystemsCatalog = {
		  "supplemental": {
			    "version": {
			      "_cldrVersion": "26",
			      "_number": "$Revision: 9732 $"
			    },
			    "generation": {
			      "_date": "$Date: 2014-02-13 11:57:02 -0600 (Thu, 13 Feb 2014) $"
			    },
			    "numberingSystems": {
			      "arab": {
			        "_digits": "٠١٢٣٤٥٦٧٨٩",
			        "_type": "numeric"
			      },
			      "arabext": {
			        "_digits": "۰۱۲۳۴۵۶۷۸۹",
			        "_type": "numeric"
			      },
			      "bali": {
			        "_digits": "᭐᭑᭒᭓᭔᭕᭖᭗᭘᭙",
			        "_type": "numeric"
			      },
			      "beng": {
			        "_digits": "০১২৩৪৫৬৭৮৯",
			        "_type": "numeric"
			      },
			      "brah": {
			        "_digits": "𑁦𑁧𑁨𑁩𑁪𑁫𑁬𑁭𑁮𑁯",
			        "_type": "numeric"
			      },
			      "cakm": {
			        "_digits": "𑄶𑄷𑄸𑄹𑄺𑄻𑄼𑄽𑄾𑄿",
			        "_type": "numeric"
			      },
			      "cham": {
			        "_digits": "꩐꩑꩒꩓꩔꩕꩖꩗꩘꩙",
			        "_type": "numeric"
			      },
			      "deva": {
			        "_digits": "०१२३४५६७८९",
			        "_type": "numeric"
			      },
			      "fullwide": {
			        "_digits": "０１２３４５６７８９",
			        "_type": "numeric"
			      },
			      "gujr": {
			        "_digits": "૦૧૨૩૪૫૬૭૮૯",
			        "_type": "numeric"
			      },
			      "guru": {
			        "_digits": "੦੧੨੩੪੫੬੭੮੯",
			        "_type": "numeric"
			      },
			      "hanidec": {
			        "_digits": "〇一二三四五六七八九",
			        "_type": "numeric"
			      },
			      "java": {
			        "_digits": "꧐꧑꧒꧓꧔꧕꧖꧗꧘꧙",
			        "_type": "numeric"
			      },
			      "kali": {
			        "_digits": "꤀꤁꤂꤃꤄꤅꤆꤇꤈꤉",
			        "_type": "numeric"
			      },
			      "khmr": {
			        "_digits": "០១២៣៤៥៦៧៨៩",
			        "_type": "numeric"
			      },
			      "knda": {
			        "_digits": "೦೧೨೩೪೫೬೭೮೯",
			        "_type": "numeric"
			      },
			      "lana": {
			        "_digits": "᪀᪁᪂᪃᪄᪅᪆᪇᪈᪉",
			        "_type": "numeric"
			      },
			      "lanatham": {
			        "_digits": "᪐᪑᪒᪓᪔᪕᪖᪗᪘᪙",
			        "_type": "numeric"
			      },
			      "laoo": {
			        "_digits": "໐໑໒໓໔໕໖໗໘໙",
			        "_type": "numeric"
			      },
			      "latn": {
			        "_digits": "0123456789",
			        "_type": "numeric"
			      },
			      "lepc": {
			        "_digits": "᱀᱁᱂᱃᱄᱅᱆᱇᱈᱉",
			        "_type": "numeric"
			      },
			      "limb": {
			        "_digits": "᥆᥇᥈᥉᥊᥋᥌᥍᥎᥏",
			        "_type": "numeric"
			      },
			      "mlym": {
			        "_digits": "൦൧൨൩൪൫൬൭൮൯",
			        "_type": "numeric"
			      },
			      "mong": {
			        "_digits": "᠐᠑᠒᠓᠔᠕᠖᠗᠘᠙",
			        "_type": "numeric"
			      },
			      "mtei": {
			        "_digits": "꯰꯱꯲꯳꯴꯵꯶꯷꯸꯹",
			        "_type": "numeric"
			      },
			      "mymr": {
			        "_digits": "၀၁၂၃၄၅၆၇၈၉",
			        "_type": "numeric"
			      },
			      "mymrshan": {
			        "_digits": "႐႑႒႓႔႕႖႗႘႙",
			        "_type": "numeric"
			      },
			      "nkoo": {
			        "_digits": "߀߁߂߃߄߅߆߇߈߉",
			        "_type": "numeric"
			      },
			      "olck": {
			        "_digits": "᱐᱑᱒᱓᱔᱕᱖᱗᱘᱙",
			        "_type": "numeric"
			      },
			      "orya": {
			        "_digits": "୦୧୨୩୪୫୬୭୮୯",
			        "_type": "numeric"
			      },
			      "osma": {
			        "_digits": "𐒠𐒡𐒢𐒣𐒤𐒥𐒦𐒧𐒨𐒩",
			        "_type": "numeric"
			      },
			      "saur": {
			        "_digits": "꣐꣑꣒꣓꣔꣕꣖꣗꣘꣙",
			        "_type": "numeric"
			      },
			      "shrd": {
			        "_digits": "𑇐𑇑𑇒𑇓𑇔𑇕𑇖𑇗𑇘𑇙",
			        "_type": "numeric"
			      },
			      "sora": {
			        "_digits": "𑃰𑃱𑃲𑃳𑃴𑃵𑃶𑃷𑃸𑃹",
			        "_type": "numeric"
			      },
			      "sund": {
			        "_digits": "᮰᮱᮲᮳᮴᮵᮶᮷᮸᮹",
			        "_type": "numeric"
			      },
			      "takr": {
			        "_digits": "𑛀𑛁𑛂𑛃𑛄𑛅𑛆𑛇𑛈𑛉",
			        "_type": "numeric"
			      },
			      "talu": {
			        "_digits": "᧐᧑᧒᧓᧔᧕᧖᧗᧘᧙",
			        "_type": "numeric"
			      },
			      "tamldec": {
			        "_digits": "௦௧௨௩௪௫௬௭௮௯",
			        "_type": "numeric"
			      },
			      "telu": {
			        "_digits": "౦౧౨౩౪౫౬౭౮౯",
			        "_type": "numeric"
			      },
			      "thai": {
			        "_digits": "๐๑๒๓๔๕๖๗๘๙",
			        "_type": "numeric"
			      },
			      "tibt": {
			        "_digits": "༠༡༢༣༤༥༦༧༨༩",
			        "_type": "numeric"
			      },
			      "vaii": {
			        "_digits": "꘠꘡꘢꘣꘤꘥꘦꘧꘨꘩",
			        "_type": "numeric"
			      },
			      "armn": {
			        "_type": "algorithmic",
			        "_rules": "armenian-upper"
			      },
			      "armnlow": {
			        "_type": "algorithmic",
			        "_rules": "armenian-lower"
			      },
			      "ethi": {
			        "_type": "algorithmic",
			        "_rules": "ethiopic"
			      },
			      "geor": {
			        "_type": "algorithmic",
			        "_rules": "georgian"
			      },
			      "grek": {
			        "_type": "algorithmic",
			        "_rules": "greek-upper"
			      },
			      "greklow": {
			        "_type": "algorithmic",
			        "_rules": "greek-lower"
			      },
			      "hanidays": {
			        "_type": "algorithmic",
			        "_rules": "zh/SpelloutRules/spellout-numbering-days"
			      },
			      "hans": {
			        "_type": "algorithmic",
			        "_rules": "zh/SpelloutRules/spellout-cardinal"
			      },
			      "hansfin": {
			        "_type": "algorithmic",
			        "_rules": "zh/SpelloutRules/spellout-cardinal-financial"
			      },
			      "hant": {
			        "_type": "algorithmic",
			        "_rules": "zh_Hant/SpelloutRules/spellout-cardinal"
			      },
			      "hantfin": {
			        "_type": "algorithmic",
			        "_rules": "zh_Hant/SpelloutRules/spellout-cardinal-financial"
			      },
			      "hebr": {
			        "_type": "algorithmic",
			        "_rules": "hebrew"
			      },
			      "jpan": {
			        "_type": "algorithmic",
			        "_rules": "ja/SpelloutRules/spellout-cardinal"
			      },
			      "jpanfin": {
			        "_type": "algorithmic",
			        "_rules": "ja/SpelloutRules/spellout-cardinal-financial"
			      },
			      "roman": {
			        "_type": "algorithmic",
			        "_rules": "roman-upper"
			      },
			      "romanlow": {
			        "_type": "algorithmic",
			        "_rules": "roman-lower"
			      },
			      "taml": {
			        "_type": "algorithmic",
			        "_rules": "tamil"
			      }
			    }
			  }
			};


var fakeEnCalendarCatalog = {
		  "main": {
			    "en-US": {
			      "identity": {
			        "version": {
			          "_cldrVersion": "26",
			          "_number": "$Revision: 10669 $"
			        },
			        "generation": {
			          "_date": "$Date: 2014-07-23 16:10:33 -0500 (Wed, 23 Jul 2014) $"
			        },
			        "language": "en",
			        "territory": "US"
			      },
			      "dates": {
			        "calendars": {
			          "gregorian": {
			            "months": {
			              "format": {
			                "abbreviated": {
			                  "1": "Jan",
			                  "2": "Feb",
			                  "3": "Mar",
			                  "4": "Apr",
			                  "5": "May",
			                  "6": "Jun",
			                  "7": "Jul",
			                  "8": "Aug",
			                  "9": "Sep",
			                  "10": "Oct",
			                  "11": "Nov",
			                  "12": "Dec"
			                },
			                "narrow": {
			                  "1": "J",
			                  "2": "F",
			                  "3": "M",
			                  "4": "A",
			                  "5": "M",
			                  "6": "J",
			                  "7": "J",
			                  "8": "A",
			                  "9": "S",
			                  "10": "O",
			                  "11": "N",
			                  "12": "D"
			                },
			                "wide": {
			                  "1": "January",
			                  "2": "February",
			                  "3": "March",
			                  "4": "April",
			                  "5": "May",
			                  "6": "June",
			                  "7": "July",
			                  "8": "August",
			                  "9": "September",
			                  "10": "October",
			                  "11": "November",
			                  "12": "December"
			                }
			              },
			              "stand-alone": {
			                "abbreviated": {
			                  "1": "Jan",
			                  "2": "Feb",
			                  "3": "Mar",
			                  "4": "Apr",
			                  "5": "May",
			                  "6": "Jun",
			                  "7": "Jul",
			                  "8": "Aug",
			                  "9": "Sep",
			                  "10": "Oct",
			                  "11": "Nov",
			                  "12": "Dec"
			                },
			                "narrow": {
			                  "1": "J",
			                  "2": "F",
			                  "3": "M",
			                  "4": "A",
			                  "5": "M",
			                  "6": "J",
			                  "7": "J",
			                  "8": "A",
			                  "9": "S",
			                  "10": "O",
			                  "11": "N",
			                  "12": "D"
			                },
			                "wide": {
			                  "1": "January",
			                  "2": "February",
			                  "3": "March",
			                  "4": "April",
			                  "5": "May",
			                  "6": "June",
			                  "7": "July",
			                  "8": "August",
			                  "9": "September",
			                  "10": "October",
			                  "11": "November",
			                  "12": "December"
			                }
			              }
			            },
			            "days": {
			              "format": {
			                "abbreviated": {
			                  "sun": "Sun",
			                  "mon": "Mon",
			                  "tue": "Tue",
			                  "wed": "Wed",
			                  "thu": "Thu",
			                  "fri": "Fri",
			                  "sat": "Sat"
			                },
			                "narrow": {
			                  "sun": "S",
			                  "mon": "M",
			                  "tue": "T",
			                  "wed": "W",
			                  "thu": "T",
			                  "fri": "F",
			                  "sat": "S"
			                },
			                "short": {
			                  "sun": "Su",
			                  "mon": "Mo",
			                  "tue": "Tu",
			                  "wed": "We",
			                  "thu": "Th",
			                  "fri": "Fr",
			                  "sat": "Sa"
			                },
			                "wide": {
			                  "sun": "Sunday",
			                  "mon": "Monday",
			                  "tue": "Tuesday",
			                  "wed": "Wednesday",
			                  "thu": "Thursday",
			                  "fri": "Friday",
			                  "sat": "Saturday"
			                }
			              },
			              "stand-alone": {
			                "abbreviated": {
			                  "sun": "Sun",
			                  "mon": "Mon",
			                  "tue": "Tue",
			                  "wed": "Wed",
			                  "thu": "Thu",
			                  "fri": "Fri",
			                  "sat": "Sat"
			                },
			                "narrow": {
			                  "sun": "S",
			                  "mon": "M",
			                  "tue": "T",
			                  "wed": "W",
			                  "thu": "T",
			                  "fri": "F",
			                  "sat": "S"
			                },
			                "short": {
			                  "sun": "Su",
			                  "mon": "Mo",
			                  "tue": "Tu",
			                  "wed": "We",
			                  "thu": "Th",
			                  "fri": "Fr",
			                  "sat": "Sa"
			                },
			                "wide": {
			                  "sun": "Sunday",
			                  "mon": "Monday",
			                  "tue": "Tuesday",
			                  "wed": "Wednesday",
			                  "thu": "Thursday",
			                  "fri": "Friday",
			                  "sat": "Saturday"
			                }
			              }
			            },
			            "quarters": {
			              "format": {
			                "abbreviated": {
			                  "1": "Q1",
			                  "2": "Q2",
			                  "3": "Q3",
			                  "4": "Q4"
			                },
			                "narrow": {
			                  "1": "1",
			                  "2": "2",
			                  "3": "3",
			                  "4": "4"
			                },
			                "wide": {
			                  "1": "1st quarter",
			                  "2": "2nd quarter",
			                  "3": "3rd quarter",
			                  "4": "4th quarter"
			                }
			              },
			              "stand-alone": {
			                "abbreviated": {
			                  "1": "Q1",
			                  "2": "Q2",
			                  "3": "Q3",
			                  "4": "Q4"
			                },
			                "narrow": {
			                  "1": "1",
			                  "2": "2",
			                  "3": "3",
			                  "4": "4"
			                },
			                "wide": {
			                  "1": "1st quarter",
			                  "2": "2nd quarter",
			                  "3": "3rd quarter",
			                  "4": "4th quarter"
			                }
			              }
			            },
			            "dayPeriods": {
			              "format": {
			                "abbreviated": {
			                  "am": "AM",
			                  "am-alt-variant": "am",
			                  "noon": "noon",
			                  "pm": "PM",
			                  "pm-alt-variant": "pm"
			                },
			                "narrow": {
			                  "am": "a",
			                  "noon": "n",
			                  "pm": "p"
			                },
			                "wide": {
			                  "am": "AM",
			                  "am-alt-variant": "am",
			                  "noon": "noon",
			                  "pm": "PM",
			                  "pm-alt-variant": "pm"
			                }
			              },
			              "stand-alone": {
			                "abbreviated": {
			                  "am": "AM",
			                  "am-alt-variant": "am",
			                  "noon": "noon",
			                  "pm": "PM",
			                  "pm-alt-variant": "pm"
			                },
			                "narrow": {
			                  "am": "a",
			                  "noon": "n",
			                  "pm": "p"
			                },
			                "wide": {
			                  "am": "AM",
			                  "am-alt-variant": "am",
			                  "noon": "noon",
			                  "pm": "PM",
			                  "pm-alt-variant": "pm"
			                }
			              }
			            },
			            "eras": {
			              "eraNames": {
			                "0": "Before Christ",
			                "0-alt-variant": "Before Common Era",
			                "1": "Anno Domini",
			                "1-alt-variant": "Common Era"
			              },
			              "eraAbbr": {
			                "0": "BC",
			                "0-alt-variant": "BCE",
			                "1": "AD",
			                "1-alt-variant": "CE"
			              },
			              "eraNarrow": {
			                "0": "B",
			                "0-alt-variant": "BCE",
			                "1": "A",
			                "1-alt-variant": "CE"
			              }
			            },
			            "dateFormats": {
			              "full": "EEEE, MMMM d, y",
			              "long": "MMMM d, y",
			              "medium": "MMM d, y",
			              "short": "M/d/yy"
			            },
			            "timeFormats": {
			              "full": "h:mm:ss a zzzz",
			              "long": "h:mm:ss a z",
			              "medium": "h:mm:ss a",
			              "short": "h:mm a"
			            },
			            "dateTimeFormats": {
			              "full": "{1} 'at' {0}",
			              "long": "{1} 'at' {0}",
			              "medium": "{1}, {0}",
			              "short": "{1}, {0}",
			              "availableFormats": {
			                "E": "ccc",
			                "EHm": "E HH:mm",
			                "EHms": "E HH:mm:ss",
			                "Ed": "d E",
			                "Ehm": "E h:mm a",
			                "Ehms": "E h:mm:ss a",
			                "Gy": "y G",
			                "GyMMM": "MMM y G",
			                "GyMMMEd": "E, MMM d, y G",
			                "GyMMMd": "MMM d, y G",
			                "H": "HH",
			                "Hm": "HH:mm",
			                "Hms": "HH:mm:ss",
			                "M": "L",
			                "MEd": "E, M/d",
			                "MMM": "LLL",
			                "MMMEd": "E, MMM d",
			                "MMMd": "MMM d",
			                "Md": "M/d",
			                "d": "d",
			                "h": "h a",
			                "hm": "h:mm a",
			                "hms": "h:mm:ss a",
			                "ms": "mm:ss",
			                "y": "y",
			                "yM": "M/y",
			                "yMEd": "E, M/d/y",
			                "yMMM": "MMM y",
			                "yMMMEd": "E, MMM d, y",
			                "yMMMd": "MMM d, y",
			                "yMd": "M/d/y",
			                "yQQQ": "QQQ y",
			                "yQQQQ": "QQQQ y"
			              },
			              "appendItems": {
			                "Day": "{0} ({2}: {1})",
			                "Day-Of-Week": "{0} {1}",
			                "Era": "{0} {1}",
			                "Hour": "{0} ({2}: {1})",
			                "Minute": "{0} ({2}: {1})",
			                "Month": "{0} ({2}: {1})",
			                "Quarter": "{0} ({2}: {1})",
			                "Second": "{0} ({2}: {1})",
			                "Timezone": "{0} {1}",
			                "Week": "{0} ({2}: {1})",
			                "Year": "{0} {1}"
			              },
			              "intervalFormats": {
			                "intervalFormatFallback": "{0} – {1}",
			                "H": {
			                  "H": "HH – HH"
			                },
			                "Hm": {
			                  "H": "HH:mm – HH:mm",
			                  "m": "HH:mm – HH:mm"
			                },
			                "Hmv": {
			                  "H": "HH:mm – HH:mm v",
			                  "m": "HH:mm – HH:mm v"
			                },
			                "Hv": {
			                  "H": "HH – HH v"
			                },
			                "M": {
			                  "M": "M – M"
			                },
			                "MEd": {
			                  "M": "E, M/d – E, M/d",
			                  "d": "E, M/d – E, M/d"
			                },
			                "MMM": {
			                  "M": "MMM – MMM"
			                },
			                "MMMEd": {
			                  "M": "E, MMM d – E, MMM d",
			                  "d": "E, MMM d – E, MMM d"
			                },
			                "MMMd": {
			                  "M": "MMM d – MMM d",
			                  "d": "MMM d – d"
			                },
			                "Md": {
			                  "M": "M/d – M/d",
			                  "d": "M/d – M/d"
			                },
			                "d": {
			                  "d": "d – d"
			                },
			                "h": {
			                  "a": "h a – h a",
			                  "h": "h – h a"
			                },
			                "hm": {
			                  "a": "h:mm a – h:mm a",
			                  "h": "h:mm – h:mm a",
			                  "m": "h:mm – h:mm a"
			                },
			                "hmv": {
			                  "a": "h:mm a – h:mm a v",
			                  "h": "h:mm – h:mm a v",
			                  "m": "h:mm – h:mm a v"
			                },
			                "hv": {
			                  "a": "h a – h a v",
			                  "h": "h – h a v"
			                },
			                "y": {
			                  "y": "y – y"
			                },
			                "yM": {
			                  "M": "M/y – M/y",
			                  "y": "M/y – M/y"
			                },
			                "yMEd": {
			                  "M": "E, M/d/y – E, M/d/y",
			                  "d": "E, M/d/y – E, M/d/y",
			                  "y": "E, M/d/y – E, M/d/y"
			                },
			                "yMMM": {
			                  "M": "MMM – MMM y",
			                  "y": "MMM y – MMM y"
			                },
			                "yMMMEd": {
			                  "M": "E, MMM d – E, MMM d, y",
			                  "d": "E, MMM d – E, MMM d, y",
			                  "y": "E, MMM d, y – E, MMM d, y"
			                },
			                "yMMMM": {
			                  "M": "MMMM – MMMM y",
			                  "y": "MMMM y – MMMM y"
			                },
			                "yMMMd": {
			                  "M": "MMM d – MMM d, y",
			                  "d": "MMM d – d, y",
			                  "y": "MMM d, y – MMM d, y"
			                },
			                "yMd": {
			                  "M": "M/d/y – M/d/y",
			                  "d": "M/d/y – M/d/y",
			                  "y": "M/d/y – M/d/y"
			                }
			              }
			            }
			          }
			        }
			      }
			    }
			  }
			};

var fakeEnTimeZoneNamesCatalog = {
		  "main": {
			    "en-US": {
			      "identity": {
			        "version": {
			          "_cldrVersion": "26",
			          "_number": "$Revision: 10669 $"
			        },
			        "generation": {
			          "_date": "$Date: 2014-07-23 16:10:33 -0500 (Wed, 23 Jul 2014) $"
			        },
			        "language": "en",
			        "territory": "US"
			      },
			      "dates": {
			        "timeZoneNames": {
			          "hourFormat": "+HH:mm;-HH:mm",
			          "gmtFormat": "GMT{0}",
			          "gmtZeroFormat": "GMT",
			          "regionFormat": "{0} Time",
			          "regionFormat-type-standard": "{0} Standard Time",
			          "regionFormat-type-daylight": "{0} Daylight Time",
			          "fallbackFormat": "{1} ({0})",
			          "zone": {
			            "America": {
			              "Adak": {
			                "exemplarCity": "Adak"
			              },
			              "Anchorage": {
			                "exemplarCity": "Anchorage"
			              },
			              "Anguilla": {
			                "exemplarCity": "Anguilla"
			              },
			              "Antigua": {
			                "exemplarCity": "Antigua"
			              },
			              "Araguaina": {
			                "exemplarCity": "Araguaina"
			              },
			              "Argentina": {
			                "La_Rioja": {
			                  "exemplarCity": "La Rioja"
			                },
			                "Rio_Gallegos": {
			                  "exemplarCity": "Rio Gallegos"
			                },
			                "Salta": {
			                  "exemplarCity": "Salta"
			                },
			                "San_Juan": {
			                  "exemplarCity": "San Juan"
			                },
			                "San_Luis": {
			                  "exemplarCity": "San Luis"
			                },
			                "Tucuman": {
			                  "exemplarCity": "Tucuman"
			                },
			                "Ushuaia": {
			                  "exemplarCity": "Ushuaia"
			                }
			              },
			              "Aruba": {
			                "exemplarCity": "Aruba"
			              },
			              "Asuncion": {
			                "exemplarCity": "Asunción"
			              },
			              "Bahia": {
			                "exemplarCity": "Bahia"
			              },
			              "Bahia_Banderas": {
			                "exemplarCity": "Bahia Banderas"
			              },
			              "Barbados": {
			                "exemplarCity": "Barbados"
			              },
			              "Belem": {
			                "exemplarCity": "Belem"
			              },
			              "Belize": {
			                "exemplarCity": "Belize"
			              },
			              "Blanc-Sablon": {
			                "exemplarCity": "Blanc-Sablon"
			              },
			              "Boa_Vista": {
			                "exemplarCity": "Boa Vista"
			              },
			              "Bogota": {
			                "exemplarCity": "Bogota"
			              },
			              "Boise": {
			                "exemplarCity": "Boise"
			              },
			              "Buenos_Aires": {
			                "exemplarCity": "Buenos Aires"
			              },
			              "Cambridge_Bay": {
			                "exemplarCity": "Cambridge Bay"
			              },
			              "Campo_Grande": {
			                "exemplarCity": "Campo Grande"
			              },
			              "Cancun": {
			                "exemplarCity": "Cancun"
			              },
			              "Caracas": {
			                "exemplarCity": "Caracas"
			              },
			              "Catamarca": {
			                "exemplarCity": "Catamarca"
			              },
			              "Cayenne": {
			                "exemplarCity": "Cayenne"
			              },
			              "Cayman": {
			                "exemplarCity": "Cayman"
			              },
			              "Chicago": {
			                "exemplarCity": "Chicago"
			              },
			              "Chihuahua": {
			                "exemplarCity": "Chihuahua"
			              },
			              "Coral_Harbour": {
			                "exemplarCity": "Atikokan"
			              },
			              "Cordoba": {
			                "exemplarCity": "Cordoba"
			              },
			              "Costa_Rica": {
			                "exemplarCity": "Costa Rica"
			              },
			              "Creston": {
			                "exemplarCity": "Creston"
			              },
			              "Cuiaba": {
			                "exemplarCity": "Cuiaba"
			              },
			              "Curacao": {
			                "exemplarCity": "Curaçao"
			              },
			              "Danmarkshavn": {
			                "exemplarCity": "Danmarkshavn"
			              },
			              "Dawson": {
			                "exemplarCity": "Dawson"
			              },
			              "Dawson_Creek": {
			                "exemplarCity": "Dawson Creek"
			              },
			              "Denver": {
			                "exemplarCity": "Denver"
			              },
			              "Detroit": {
			                "exemplarCity": "Detroit"
			              },
			              "Dominica": {
			                "exemplarCity": "Dominica"
			              },
			              "Edmonton": {
			                "exemplarCity": "Edmonton"
			              },
			              "Eirunepe": {
			                "exemplarCity": "Eirunepe"
			              },
			              "El_Salvador": {
			                "exemplarCity": "El Salvador"
			              },
			              "Fortaleza": {
			                "exemplarCity": "Fortaleza"
			              },
			              "Glace_Bay": {
			                "exemplarCity": "Glace Bay"
			              },
			              "Godthab": {
			                "exemplarCity": "Nuuk"
			              },
			              "Goose_Bay": {
			                "exemplarCity": "Goose Bay"
			              },
			              "Grand_Turk": {
			                "exemplarCity": "Grand Turk"
			              },
			              "Grenada": {
			                "exemplarCity": "Grenada"
			              },
			              "Guadeloupe": {
			                "exemplarCity": "Guadeloupe"
			              },
			              "Guatemala": {
			                "exemplarCity": "Guatemala"
			              },
			              "Guayaquil": {
			                "exemplarCity": "Guayaquil"
			              },
			              "Guyana": {
			                "exemplarCity": "Guyana"
			              },
			              "Halifax": {
			                "exemplarCity": "Halifax"
			              },
			              "Havana": {
			                "exemplarCity": "Havana"
			              },
			              "Hermosillo": {
			                "exemplarCity": "Hermosillo"
			              },
			              "Indiana": {
			                "Knox": {
			                  "exemplarCity": "Knox, Indiana"
			                },
			                "Marengo": {
			                  "exemplarCity": "Marengo, Indiana"
			                },
			                "Petersburg": {
			                  "exemplarCity": "Petersburg, Indiana"
			                },
			                "Tell_City": {
			                  "exemplarCity": "Tell City, Indiana"
			                },
			                "Vevay": {
			                  "exemplarCity": "Vevay, Indiana"
			                },
			                "Vincennes": {
			                  "exemplarCity": "Vincennes, Indiana"
			                },
			                "Winamac": {
			                  "exemplarCity": "Winamac, Indiana"
			                }
			              },
			              "Indianapolis": {
			                "exemplarCity": "Indianapolis"
			              },
			              "Inuvik": {
			                "exemplarCity": "Inuvik"
			              },
			              "Iqaluit": {
			                "exemplarCity": "Iqaluit"
			              },
			              "Jamaica": {
			                "exemplarCity": "Jamaica"
			              },
			              "Jujuy": {
			                "exemplarCity": "Jujuy"
			              },
			              "Juneau": {
			                "exemplarCity": "Juneau"
			              },
			              "Kentucky": {
			                "Monticello": {
			                  "exemplarCity": "Monticello, Kentucky"
			                }
			              },
			              "Kralendijk": {
			                "exemplarCity": "Kralendijk"
			              },
			              "La_Paz": {
			                "exemplarCity": "La Paz"
			              },
			              "Lima": {
			                "exemplarCity": "Lima"
			              },
			              "Los_Angeles": {
			                "exemplarCity": "Los Angeles"
			              },
			              "Louisville": {
			                "exemplarCity": "Louisville"
			              },
			              "Lower_Princes": {
			                "exemplarCity": "Lower Prince’s Quarter"
			              },
			              "Maceio": {
			                "exemplarCity": "Maceio"
			              },
			              "Managua": {
			                "exemplarCity": "Managua"
			              },
			              "Manaus": {
			                "exemplarCity": "Manaus"
			              },
			              "Marigot": {
			                "exemplarCity": "Marigot"
			              },
			              "Martinique": {
			                "exemplarCity": "Martinique"
			              },
			              "Matamoros": {
			                "exemplarCity": "Matamoros"
			              },
			              "Mazatlan": {
			                "exemplarCity": "Mazatlan"
			              },
			              "Mendoza": {
			                "exemplarCity": "Mendoza"
			              },
			              "Menominee": {
			                "exemplarCity": "Menominee"
			              },
			              "Merida": {
			                "exemplarCity": "Merida"
			              },
			              "Metlakatla": {
			                "exemplarCity": "Metlakatla"
			              },
			              "Mexico_City": {
			                "exemplarCity": "Mexico City"
			              },
			              "Miquelon": {
			                "exemplarCity": "Miquelon"
			              },
			              "Moncton": {
			                "exemplarCity": "Moncton"
			              },
			              "Monterrey": {
			                "exemplarCity": "Monterrey"
			              },
			              "Montevideo": {
			                "exemplarCity": "Montevideo"
			              },
			              "Montserrat": {
			                "exemplarCity": "Montserrat"
			              },
			              "Nassau": {
			                "exemplarCity": "Nassau"
			              },
			              "New_York": {
			                "exemplarCity": "New York"
			              },
			              "Nipigon": {
			                "exemplarCity": "Nipigon"
			              },
			              "Nome": {
			                "exemplarCity": "Nome"
			              },
			              "Noronha": {
			                "exemplarCity": "Noronha"
			              },
			              "North_Dakota": {
			                "Beulah": {
			                  "exemplarCity": "Beulah, North Dakota"
			                },
			                "Center": {
			                  "exemplarCity": "Center, North Dakota"
			                },
			                "New_Salem": {
			                  "exemplarCity": "New Salem, North Dakota"
			                }
			              },
			              "Ojinaga": {
			                "exemplarCity": "Ojinaga"
			              },
			              "Panama": {
			                "exemplarCity": "Panama"
			              },
			              "Pangnirtung": {
			                "exemplarCity": "Pangnirtung"
			              },
			              "Paramaribo": {
			                "exemplarCity": "Paramaribo"
			              },
			              "Phoenix": {
			                "exemplarCity": "Phoenix"
			              },
			              "Port-au-Prince": {
			                "exemplarCity": "Port-au-Prince"
			              },
			              "Port_of_Spain": {
			                "exemplarCity": "Port of Spain"
			              },
			              "Porto_Velho": {
			                "exemplarCity": "Porto Velho"
			              },
			              "Puerto_Rico": {
			                "exemplarCity": "Puerto Rico"
			              },
			              "Rainy_River": {
			                "exemplarCity": "Rainy River"
			              },
			              "Rankin_Inlet": {
			                "exemplarCity": "Rankin Inlet"
			              },
			              "Recife": {
			                "exemplarCity": "Recife"
			              },
			              "Regina": {
			                "exemplarCity": "Regina"
			              },
			              "Resolute": {
			                "exemplarCity": "Resolute"
			              },
			              "Rio_Branco": {
			                "exemplarCity": "Rio Branco"
			              },
			              "Santa_Isabel": {
			                "exemplarCity": "Santa Isabel"
			              },
			              "Santarem": {
			                "exemplarCity": "Santarem"
			              },
			              "Santiago": {
			                "exemplarCity": "Santiago"
			              },
			              "Santo_Domingo": {
			                "exemplarCity": "Santo Domingo"
			              },
			              "Sao_Paulo": {
			                "exemplarCity": "Sao Paulo"
			              },
			              "Scoresbysund": {
			                "exemplarCity": "Ittoqqortoormiit"
			              },
			              "Sitka": {
			                "exemplarCity": "Sitka"
			              },
			              "St_Barthelemy": {
			                "exemplarCity": "St. Barthélemy"
			              },
			              "St_Johns": {
			                "exemplarCity": "St. John’s"
			              },
			              "St_Kitts": {
			                "exemplarCity": "St. Kitts"
			              },
			              "St_Lucia": {
			                "exemplarCity": "St. Lucia"
			              },
			              "St_Thomas": {
			                "exemplarCity": "St. Thomas"
			              },
			              "St_Vincent": {
			                "exemplarCity": "St. Vincent"
			              },
			              "Swift_Current": {
			                "exemplarCity": "Swift Current"
			              },
			              "Tegucigalpa": {
			                "exemplarCity": "Tegucigalpa"
			              },
			              "Thule": {
			                "exemplarCity": "Thule"
			              },
			              "Thunder_Bay": {
			                "exemplarCity": "Thunder Bay"
			              },
			              "Tijuana": {
			                "exemplarCity": "Tijuana"
			              },
			              "Toronto": {
			                "exemplarCity": "Toronto"
			              },
			              "Tortola": {
			                "exemplarCity": "Tortola"
			              },
			              "Vancouver": {
			                "exemplarCity": "Vancouver"
			              },
			              "Whitehorse": {
			                "exemplarCity": "Whitehorse"
			              },
			              "Winnipeg": {
			                "exemplarCity": "Winnipeg"
			              },
			              "Yakutat": {
			                "exemplarCity": "Yakutat"
			              },
			              "Yellowknife": {
			                "exemplarCity": "Yellowknife"
			              }
			            },
			            "Atlantic": {
			              "Azores": {
			                "exemplarCity": "Azores"
			              },
			              "Bermuda": {
			                "exemplarCity": "Bermuda"
			              },
			              "Canary": {
			                "exemplarCity": "Canary"
			              },
			              "Cape_Verde": {
			                "exemplarCity": "Cape Verde"
			              },
			              "Faeroe": {
			                "exemplarCity": "Faroe"
			              },
			              "Madeira": {
			                "exemplarCity": "Madeira"
			              },
			              "Reykjavik": {
			                "exemplarCity": "Reykjavik"
			              },
			              "South_Georgia": {
			                "exemplarCity": "South Georgia"
			              },
			              "St_Helena": {
			                "exemplarCity": "St. Helena"
			              },
			              "Stanley": {
			                "exemplarCity": "Stanley"
			              }
			            },
			            "Europe": {
			              "Amsterdam": {
			                "exemplarCity": "Amsterdam"
			              },
			              "Andorra": {
			                "exemplarCity": "Andorra"
			              },
			              "Athens": {
			                "exemplarCity": "Athens"
			              },
			              "Belgrade": {
			                "exemplarCity": "Belgrade"
			              },
			              "Berlin": {
			                "exemplarCity": "Berlin"
			              },
			              "Bratislava": {
			                "exemplarCity": "Bratislava"
			              },
			              "Brussels": {
			                "exemplarCity": "Brussels"
			              },
			              "Bucharest": {
			                "exemplarCity": "Bucharest"
			              },
			              "Budapest": {
			                "exemplarCity": "Budapest"
			              },
			              "Busingen": {
			                "exemplarCity": "Busingen"
			              },
			              "Chisinau": {
			                "exemplarCity": "Chisinau"
			              },
			              "Copenhagen": {
			                "exemplarCity": "Copenhagen"
			              },
			              "Dublin": {
			                "long": {
			                  "daylight": "Irish Standard Time"
			                },
			                "exemplarCity": "Dublin"
			              },
			              "Gibraltar": {
			                "exemplarCity": "Gibraltar"
			              },
			              "Guernsey": {
			                "exemplarCity": "Guernsey"
			              },
			              "Helsinki": {
			                "exemplarCity": "Helsinki"
			              },
			              "Isle_of_Man": {
			                "exemplarCity": "Isle of Man"
			              },
			              "Istanbul": {
			                "exemplarCity": "Istanbul"
			              },
			              "Jersey": {
			                "exemplarCity": "Jersey"
			              },
			              "Kaliningrad": {
			                "exemplarCity": "Kaliningrad"
			              },
			              "Kiev": {
			                "exemplarCity": "Kiev"
			              },
			              "Lisbon": {
			                "exemplarCity": "Lisbon"
			              },
			              "Ljubljana": {
			                "exemplarCity": "Ljubljana"
			              },
			              "London": {
			                "long": {
			                  "daylight": "British Summer Time"
			                },
			                "exemplarCity": "London"
			              },
			              "Luxembourg": {
			                "exemplarCity": "Luxembourg"
			              },
			              "Madrid": {
			                "exemplarCity": "Madrid"
			              },
			              "Malta": {
			                "exemplarCity": "Malta"
			              },
			              "Mariehamn": {
			                "exemplarCity": "Mariehamn"
			              },
			              "Minsk": {
			                "exemplarCity": "Minsk"
			              },
			              "Monaco": {
			                "exemplarCity": "Monaco"
			              },
			              "Moscow": {
			                "exemplarCity": "Moscow"
			              },
			              "Oslo": {
			                "exemplarCity": "Oslo"
			              },
			              "Paris": {
			                "exemplarCity": "Paris"
			              },
			              "Podgorica": {
			                "exemplarCity": "Podgorica"
			              },
			              "Prague": {
			                "exemplarCity": "Prague"
			              },
			              "Riga": {
			                "exemplarCity": "Riga"
			              },
			              "Rome": {
			                "exemplarCity": "Rome"
			              },
			              "Samara": {
			                "exemplarCity": "Samara"
			              },
			              "San_Marino": {
			                "exemplarCity": "San Marino"
			              },
			              "Sarajevo": {
			                "exemplarCity": "Sarajevo"
			              },
			              "Simferopol": {
			                "exemplarCity": "Simferopol"
			              },
			              "Skopje": {
			                "exemplarCity": "Skopje"
			              },
			              "Sofia": {
			                "exemplarCity": "Sofia"
			              },
			              "Stockholm": {
			                "exemplarCity": "Stockholm"
			              },
			              "Tallinn": {
			                "exemplarCity": "Tallinn"
			              },
			              "Tirane": {
			                "exemplarCity": "Tirane"
			              },
			              "Uzhgorod": {
			                "exemplarCity": "Uzhhorod"
			              },
			              "Vaduz": {
			                "exemplarCity": "Vaduz"
			              },
			              "Vatican": {
			                "exemplarCity": "Vatican"
			              },
			              "Vienna": {
			                "exemplarCity": "Vienna"
			              },
			              "Vilnius": {
			                "exemplarCity": "Vilnius"
			              },
			              "Volgograd": {
			                "exemplarCity": "Volgograd"
			              },
			              "Warsaw": {
			                "exemplarCity": "Warsaw"
			              },
			              "Zagreb": {
			                "exemplarCity": "Zagreb"
			              },
			              "Zaporozhye": {
			                "exemplarCity": "Zaporozhye"
			              },
			              "Zurich": {
			                "exemplarCity": "Zurich"
			              }
			            },
			            "Africa": {
			              "Abidjan": {
			                "exemplarCity": "Abidjan"
			              },
			              "Accra": {
			                "exemplarCity": "Accra"
			              },
			              "Addis_Ababa": {
			                "exemplarCity": "Addis Ababa"
			              },
			              "Algiers": {
			                "exemplarCity": "Algiers"
			              },
			              "Asmera": {
			                "exemplarCity": "Asmara"
			              },
			              "Bamako": {
			                "exemplarCity": "Bamako"
			              },
			              "Bangui": {
			                "exemplarCity": "Bangui"
			              },
			              "Banjul": {
			                "exemplarCity": "Banjul"
			              },
			              "Bissau": {
			                "exemplarCity": "Bissau"
			              },
			              "Blantyre": {
			                "exemplarCity": "Blantyre"
			              },
			              "Brazzaville": {
			                "exemplarCity": "Brazzaville"
			              },
			              "Bujumbura": {
			                "exemplarCity": "Bujumbura"
			              },
			              "Cairo": {
			                "exemplarCity": "Cairo"
			              },
			              "Casablanca": {
			                "exemplarCity": "Casablanca"
			              },
			              "Ceuta": {
			                "exemplarCity": "Ceuta"
			              },
			              "Conakry": {
			                "exemplarCity": "Conakry"
			              },
			              "Dakar": {
			                "exemplarCity": "Dakar"
			              },
			              "Dar_es_Salaam": {
			                "exemplarCity": "Dar es Salaam"
			              },
			              "Djibouti": {
			                "exemplarCity": "Djibouti"
			              },
			              "Douala": {
			                "exemplarCity": "Douala"
			              },
			              "El_Aaiun": {
			                "exemplarCity": "El Aaiun"
			              },
			              "Freetown": {
			                "exemplarCity": "Freetown"
			              },
			              "Gaborone": {
			                "exemplarCity": "Gaborone"
			              },
			              "Harare": {
			                "exemplarCity": "Harare"
			              },
			              "Johannesburg": {
			                "exemplarCity": "Johannesburg"
			              },
			              "Juba": {
			                "exemplarCity": "Juba"
			              },
			              "Kampala": {
			                "exemplarCity": "Kampala"
			              },
			              "Khartoum": {
			                "exemplarCity": "Khartoum"
			              },
			              "Kigali": {
			                "exemplarCity": "Kigali"
			              },
			              "Kinshasa": {
			                "exemplarCity": "Kinshasa"
			              },
			              "Lagos": {
			                "exemplarCity": "Lagos"
			              },
			              "Libreville": {
			                "exemplarCity": "Libreville"
			              },
			              "Lome": {
			                "exemplarCity": "Lome"
			              },
			              "Luanda": {
			                "exemplarCity": "Luanda"
			              },
			              "Lubumbashi": {
			                "exemplarCity": "Lubumbashi"
			              },
			              "Lusaka": {
			                "exemplarCity": "Lusaka"
			              },
			              "Malabo": {
			                "exemplarCity": "Malabo"
			              },
			              "Maputo": {
			                "exemplarCity": "Maputo"
			              },
			              "Maseru": {
			                "exemplarCity": "Maseru"
			              },
			              "Mbabane": {
			                "exemplarCity": "Mbabane"
			              },
			              "Mogadishu": {
			                "exemplarCity": "Mogadishu"
			              },
			              "Monrovia": {
			                "exemplarCity": "Monrovia"
			              },
			              "Nairobi": {
			                "exemplarCity": "Nairobi"
			              },
			              "Ndjamena": {
			                "exemplarCity": "Ndjamena"
			              },
			              "Niamey": {
			                "exemplarCity": "Niamey"
			              },
			              "Nouakchott": {
			                "exemplarCity": "Nouakchott"
			              },
			              "Ouagadougou": {
			                "exemplarCity": "Ouagadougou"
			              },
			              "Porto-Novo": {
			                "exemplarCity": "Porto-Novo"
			              },
			              "Sao_Tome": {
			                "exemplarCity": "São Tomé"
			              },
			              "Tripoli": {
			                "exemplarCity": "Tripoli"
			              },
			              "Tunis": {
			                "exemplarCity": "Tunis"
			              },
			              "Windhoek": {
			                "exemplarCity": "Windhoek"
			              }
			            },
			            "Asia": {
			              "Aden": {
			                "exemplarCity": "Aden"
			              },
			              "Almaty": {
			                "exemplarCity": "Almaty"
			              },
			              "Amman": {
			                "exemplarCity": "Amman"
			              },
			              "Anadyr": {
			                "exemplarCity": "Anadyr"
			              },
			              "Aqtau": {
			                "exemplarCity": "Aqtau"
			              },
			              "Aqtobe": {
			                "exemplarCity": "Aqtobe"
			              },
			              "Ashgabat": {
			                "exemplarCity": "Ashgabat"
			              },
			              "Baghdad": {
			                "exemplarCity": "Baghdad"
			              },
			              "Bahrain": {
			                "exemplarCity": "Bahrain"
			              },
			              "Baku": {
			                "exemplarCity": "Baku"
			              },
			              "Bangkok": {
			                "exemplarCity": "Bangkok"
			              },
			              "Beirut": {
			                "exemplarCity": "Beirut"
			              },
			              "Bishkek": {
			                "exemplarCity": "Bishkek"
			              },
			              "Brunei": {
			                "exemplarCity": "Brunei"
			              },
			              "Calcutta": {
			                "exemplarCity": "Kolkata"
			              },
			              "Chita": {
			                "exemplarCity": "Chita"
			              },
			              "Choibalsan": {
			                "exemplarCity": "Choibalsan"
			              },
			              "Colombo": {
			                "exemplarCity": "Colombo"
			              },
			              "Damascus": {
			                "exemplarCity": "Damascus"
			              },
			              "Dhaka": {
			                "exemplarCity": "Dhaka"
			              },
			              "Dili": {
			                "exemplarCity": "Dili"
			              },
			              "Dubai": {
			                "exemplarCity": "Dubai"
			              },
			              "Dushanbe": {
			                "exemplarCity": "Dushanbe"
			              },
			              "Gaza": {
			                "exemplarCity": "Gaza"
			              },
			              "Hebron": {
			                "exemplarCity": "Hebron"
			              },
			              "Hong_Kong": {
			                "exemplarCity": "Hong Kong"
			              },
			              "Hovd": {
			                "exemplarCity": "Hovd"
			              },
			              "Irkutsk": {
			                "exemplarCity": "Irkutsk"
			              },
			              "Jakarta": {
			                "exemplarCity": "Jakarta"
			              },
			              "Jayapura": {
			                "exemplarCity": "Jayapura"
			              },
			              "Jerusalem": {
			                "exemplarCity": "Jerusalem"
			              },
			              "Kabul": {
			                "exemplarCity": "Kabul"
			              },
			              "Kamchatka": {
			                "exemplarCity": "Kamchatka"
			              },
			              "Karachi": {
			                "exemplarCity": "Karachi"
			              },
			              "Katmandu": {
			                "exemplarCity": "Kathmandu"
			              },
			              "Khandyga": {
			                "exemplarCity": "Khandyga"
			              },
			              "Krasnoyarsk": {
			                "exemplarCity": "Krasnoyarsk"
			              },
			              "Kuala_Lumpur": {
			                "exemplarCity": "Kuala Lumpur"
			              },
			              "Kuching": {
			                "exemplarCity": "Kuching"
			              },
			              "Kuwait": {
			                "exemplarCity": "Kuwait"
			              },
			              "Macau": {
			                "exemplarCity": "Macau"
			              },
			              "Magadan": {
			                "exemplarCity": "Magadan"
			              },
			              "Makassar": {
			                "exemplarCity": "Makassar"
			              },
			              "Manila": {
			                "exemplarCity": "Manila"
			              },
			              "Muscat": {
			                "exemplarCity": "Muscat"
			              },
			              "Nicosia": {
			                "exemplarCity": "Nicosia"
			              },
			              "Novokuznetsk": {
			                "exemplarCity": "Novokuznetsk"
			              },
			              "Novosibirsk": {
			                "exemplarCity": "Novosibirsk"
			              },
			              "Omsk": {
			                "exemplarCity": "Omsk"
			              },
			              "Oral": {
			                "exemplarCity": "Oral"
			              },
			              "Phnom_Penh": {
			                "exemplarCity": "Phnom Penh"
			              },
			              "Pontianak": {
			                "exemplarCity": "Pontianak"
			              },
			              "Pyongyang": {
			                "exemplarCity": "Pyongyang"
			              },
			              "Qatar": {
			                "exemplarCity": "Qatar"
			              },
			              "Qyzylorda": {
			                "exemplarCity": "Qyzylorda"
			              },
			              "Rangoon": {
			                "exemplarCity": "Rangoon"
			              },
			              "Riyadh": {
			                "exemplarCity": "Riyadh"
			              },
			              "Saigon": {
			                "exemplarCity": "Ho Chi Minh City"
			              },
			              "Sakhalin": {
			                "exemplarCity": "Sakhalin"
			              },
			              "Samarkand": {
			                "exemplarCity": "Samarkand"
			              },
			              "Seoul": {
			                "exemplarCity": "Seoul"
			              },
			              "Shanghai": {
			                "exemplarCity": "Shanghai"
			              },
			              "Singapore": {
			                "exemplarCity": "Singapore"
			              },
			              "Srednekolymsk": {
			                "exemplarCity": "Srednekolymsk"
			              },
			              "Taipei": {
			                "exemplarCity": "Taipei"
			              },
			              "Tashkent": {
			                "exemplarCity": "Tashkent"
			              },
			              "Tbilisi": {
			                "exemplarCity": "Tbilisi"
			              },
			              "Tehran": {
			                "exemplarCity": "Tehran"
			              },
			              "Thimphu": {
			                "exemplarCity": "Thimphu"
			              },
			              "Tokyo": {
			                "exemplarCity": "Tokyo"
			              },
			              "Ulaanbaatar": {
			                "exemplarCity": "Ulaanbaatar"
			              },
			              "Urumqi": {
			                "exemplarCity": "Urumqi"
			              },
			              "Ust-Nera": {
			                "exemplarCity": "Ust-Nera"
			              },
			              "Vientiane": {
			                "exemplarCity": "Vientiane"
			              },
			              "Vladivostok": {
			                "exemplarCity": "Vladivostok"
			              },
			              "Yakutsk": {
			                "exemplarCity": "Yakutsk"
			              },
			              "Yekaterinburg": {
			                "exemplarCity": "Yekaterinburg"
			              },
			              "Yerevan": {
			                "exemplarCity": "Yerevan"
			              }
			            },
			            "Indian": {
			              "Antananarivo": {
			                "exemplarCity": "Antananarivo"
			              },
			              "Chagos": {
			                "exemplarCity": "Chagos"
			              },
			              "Christmas": {
			                "exemplarCity": "Christmas"
			              },
			              "Cocos": {
			                "exemplarCity": "Cocos"
			              },
			              "Comoro": {
			                "exemplarCity": "Comoro"
			              },
			              "Kerguelen": {
			                "exemplarCity": "Kerguelen"
			              },
			              "Mahe": {
			                "exemplarCity": "Mahe"
			              },
			              "Maldives": {
			                "exemplarCity": "Maldives"
			              },
			              "Mauritius": {
			                "exemplarCity": "Mauritius"
			              },
			              "Mayotte": {
			                "exemplarCity": "Mayotte"
			              },
			              "Reunion": {
			                "exemplarCity": "Réunion"
			              }
			            },
			            "Australia": {
			              "Adelaide": {
			                "exemplarCity": "Adelaide"
			              },
			              "Brisbane": {
			                "exemplarCity": "Brisbane"
			              },
			              "Broken_Hill": {
			                "exemplarCity": "Broken Hill"
			              },
			              "Currie": {
			                "exemplarCity": "Currie"
			              },
			              "Darwin": {
			                "exemplarCity": "Darwin"
			              },
			              "Eucla": {
			                "exemplarCity": "Eucla"
			              },
			              "Hobart": {
			                "exemplarCity": "Hobart"
			              },
			              "Lindeman": {
			                "exemplarCity": "Lindeman"
			              },
			              "Lord_Howe": {
			                "exemplarCity": "Lord Howe"
			              },
			              "Melbourne": {
			                "exemplarCity": "Melbourne"
			              },
			              "Perth": {
			                "exemplarCity": "Perth"
			              },
			              "Sydney": {
			                "exemplarCity": "Sydney"
			              }
			            },
			            "Pacific": {
			              "Apia": {
			                "exemplarCity": "Apia"
			              },
			              "Auckland": {
			                "exemplarCity": "Auckland"
			              },
			              "Chatham": {
			                "exemplarCity": "Chatham"
			              },
			              "Easter": {
			                "exemplarCity": "Easter"
			              },
			              "Efate": {
			                "exemplarCity": "Efate"
			              },
			              "Enderbury": {
			                "exemplarCity": "Enderbury"
			              },
			              "Fakaofo": {
			                "exemplarCity": "Fakaofo"
			              },
			              "Fiji": {
			                "exemplarCity": "Fiji"
			              },
			              "Funafuti": {
			                "exemplarCity": "Funafuti"
			              },
			              "Galapagos": {
			                "exemplarCity": "Galapagos"
			              },
			              "Gambier": {
			                "exemplarCity": "Gambier"
			              },
			              "Guadalcanal": {
			                "exemplarCity": "Guadalcanal"
			              },
			              "Guam": {
			                "exemplarCity": "Guam"
			              },
			              "Honolulu": {
			                "short": {
			                  "generic": "HST",
			                  "standard": "HST",
			                  "daylight": "HDT"
			                },
			                "exemplarCity": "Honolulu"
			              },
			              "Johnston": {
			                "exemplarCity": "Johnston"
			              },
			              "Kiritimati": {
			                "exemplarCity": "Kiritimati"
			              },
			              "Kosrae": {
			                "exemplarCity": "Kosrae"
			              },
			              "Kwajalein": {
			                "exemplarCity": "Kwajalein"
			              },
			              "Majuro": {
			                "exemplarCity": "Majuro"
			              },
			              "Marquesas": {
			                "exemplarCity": "Marquesas"
			              },
			              "Midway": {
			                "exemplarCity": "Midway"
			              },
			              "Nauru": {
			                "exemplarCity": "Nauru"
			              },
			              "Niue": {
			                "exemplarCity": "Niue"
			              },
			              "Norfolk": {
			                "exemplarCity": "Norfolk"
			              },
			              "Noumea": {
			                "exemplarCity": "Noumea"
			              },
			              "Pago_Pago": {
			                "exemplarCity": "Pago Pago"
			              },
			              "Palau": {
			                "exemplarCity": "Palau"
			              },
			              "Pitcairn": {
			                "exemplarCity": "Pitcairn"
			              },
			              "Ponape": {
			                "exemplarCity": "Pohnpei"
			              },
			              "Port_Moresby": {
			                "exemplarCity": "Port Moresby"
			              },
			              "Rarotonga": {
			                "exemplarCity": "Rarotonga"
			              },
			              "Saipan": {
			                "exemplarCity": "Saipan"
			              },
			              "Tahiti": {
			                "exemplarCity": "Tahiti"
			              },
			              "Tarawa": {
			                "exemplarCity": "Tarawa"
			              },
			              "Tongatapu": {
			                "exemplarCity": "Tongatapu"
			              },
			              "Truk": {
			                "exemplarCity": "Chuuk"
			              },
			              "Wake": {
			                "exemplarCity": "Wake"
			              },
			              "Wallis": {
			                "exemplarCity": "Wallis"
			              }
			            },
			            "Arctic": {
			              "Longyearbyen": {
			                "exemplarCity": "Longyearbyen"
			              }
			            },
			            "Antarctica": {
			              "Casey": {
			                "exemplarCity": "Casey"
			              },
			              "Davis": {
			                "exemplarCity": "Davis"
			              },
			              "DumontDUrville": {
			                "exemplarCity": "Dumont d’Urville"
			              },
			              "Macquarie": {
			                "exemplarCity": "Macquarie"
			              },
			              "Mawson": {
			                "exemplarCity": "Mawson"
			              },
			              "McMurdo": {
			                "exemplarCity": "McMurdo"
			              },
			              "Palmer": {
			                "exemplarCity": "Palmer"
			              },
			              "Rothera": {
			                "exemplarCity": "Rothera"
			              },
			              "Syowa": {
			                "exemplarCity": "Syowa"
			              },
			              "Troll": {
			                "exemplarCity": "Troll"
			              },
			              "Vostok": {
			                "exemplarCity": "Vostok"
			              }
			            },
			            "Etc": {
			              "GMT": {
			                "exemplarCity": "GMT"
			              },
			              "GMT1": {
			                "exemplarCity": "GMT+1"
			              },
			              "GMT10": {
			                "exemplarCity": "GMT+10"
			              },
			              "GMT11": {
			                "exemplarCity": "GMT+11"
			              },
			              "GMT12": {
			                "exemplarCity": "GMT+12"
			              },
			              "GMT2": {
			                "exemplarCity": "GMT+2"
			              },
			              "GMT3": {
			                "exemplarCity": "GMT+3"
			              },
			              "GMT4": {
			                "exemplarCity": "GMT+4"
			              },
			              "GMT5": {
			                "exemplarCity": "GMT+5"
			              },
			              "GMT6": {
			                "exemplarCity": "GMT+6"
			              },
			              "GMT7": {
			                "exemplarCity": "GMT+7"
			              },
			              "GMT8": {
			                "exemplarCity": "GMT+8"
			              },
			              "GMT9": {
			                "exemplarCity": "GMT+9"
			              },
			              "GMT-1": {
			                "exemplarCity": "GMT-1"
			              },
			              "GMT-10": {
			                "exemplarCity": "GMT-10"
			              },
			              "GMT-11": {
			                "exemplarCity": "GMT-11"
			              },
			              "GMT-12": {
			                "exemplarCity": "GMT-12"
			              },
			              "GMT-13": {
			                "exemplarCity": "GMT-13"
			              },
			              "GMT-14": {
			                "exemplarCity": "GMT-14"
			              },
			              "GMT-2": {
			                "exemplarCity": "GMT-2"
			              },
			              "GMT-3": {
			                "exemplarCity": "GMT-3"
			              },
			              "GMT-4": {
			                "exemplarCity": "GMT-4"
			              },
			              "GMT-5": {
			                "exemplarCity": "GMT-5"
			              },
			              "GMT-6": {
			                "exemplarCity": "GMT-6"
			              },
			              "GMT-7": {
			                "exemplarCity": "GMT-7"
			              },
			              "GMT-8": {
			                "exemplarCity": "GMT-8"
			              },
			              "GMT-9": {
			                "exemplarCity": "GMT-9"
			              },
			              "Unknown": {
			                "exemplarCity": "Unknown City"
			              }
			            }
			          },
			          "metazone": {
			            "Acre": {
			              "long": {
			                "generic": "Acre Time",
			                "standard": "Acre Standard Time",
			                "daylight": "Acre Summer Time"
			              }
			            },
			            "Afghanistan": {
			              "long": {
			                "standard": "Afghanistan Time"
			              }
			            },
			            "Africa_Central": {
			              "long": {
			                "standard": "Central Africa Time"
			              }
			            },
			            "Africa_Eastern": {
			              "long": {
			                "standard": "East Africa Time"
			              }
			            },
			            "Africa_Southern": {
			              "long": {
			                "standard": "South Africa Standard Time"
			              }
			            },
			            "Africa_Western": {
			              "long": {
			                "generic": "West Africa Time",
			                "standard": "West Africa Standard Time",
			                "daylight": "West Africa Summer Time"
			              }
			            },
			            "Alaska": {
			              "long": {
			                "generic": "Alaska Time",
			                "standard": "Alaska Standard Time",
			                "daylight": "Alaska Daylight Time"
			              },
			              "short": {
			                "generic": "AKT",
			                "standard": "AKST",
			                "daylight": "AKDT"
			              }
			            },
			            "Almaty": {
			              "long": {
			                "generic": "Almaty Time",
			                "standard": "Almaty Standard Time",
			                "daylight": "Almaty Summer Time"
			              }
			            },
			            "Amazon": {
			              "long": {
			                "generic": "Amazon Time",
			                "standard": "Amazon Standard Time",
			                "daylight": "Amazon Summer Time"
			              }
			            },
			            "America_Central": {
			              "long": {
			                "generic": "Central Time",
			                "standard": "Central Standard Time",
			                "daylight": "Central Daylight Time"
			              },
			              "short": {
			                "generic": "CT",
			                "standard": "CST",
			                "daylight": "CDT"
			              }
			            },
			            "America_Eastern": {
			              "long": {
			                "generic": "Eastern Time",
			                "standard": "Eastern Standard Time",
			                "daylight": "Eastern Daylight Time"
			              },
			              "short": {
			                "generic": "ET",
			                "standard": "EST",
			                "daylight": "EDT"
			              }
			            },
			            "America_Mountain": {
			              "long": {
			                "generic": "Mountain Time",
			                "standard": "Mountain Standard Time",
			                "daylight": "Mountain Daylight Time"
			              },
			              "short": {
			                "generic": "MT",
			                "standard": "MST",
			                "daylight": "MDT"
			              }
			            },
			            "America_Pacific": {
			              "long": {
			                "generic": "Pacific Time",
			                "standard": "Pacific Standard Time",
			                "daylight": "Pacific Daylight Time"
			              },
			              "short": {
			                "generic": "PT",
			                "standard": "PST",
			                "daylight": "PDT"
			              }
			            },
			            "Anadyr": {
			              "long": {
			                "generic": "Anadyr Time",
			                "standard": "Anadyr Standard Time",
			                "daylight": "Anadyr Summer Time"
			              }
			            },
			            "Apia": {
			              "long": {
			                "generic": "Apia Time",
			                "standard": "Apia Standard Time",
			                "daylight": "Apia Daylight Time"
			              }
			            },
			            "Aqtau": {
			              "long": {
			                "generic": "Aqtau Time",
			                "standard": "Aqtau Standard Time",
			                "daylight": "Aqtau Summer Time"
			              }
			            },
			            "Aqtobe": {
			              "long": {
			                "generic": "Aqtobe Time",
			                "standard": "Aqtobe Standard Time",
			                "daylight": "Aqtobe Summer Time"
			              }
			            },
			            "Arabian": {
			              "long": {
			                "generic": "Arabian Time",
			                "standard": "Arabian Standard Time",
			                "daylight": "Arabian Daylight Time"
			              }
			            },
			            "Argentina": {
			              "long": {
			                "generic": "Argentina Time",
			                "standard": "Argentina Standard Time",
			                "daylight": "Argentina Summer Time"
			              }
			            },
			            "Argentina_Western": {
			              "long": {
			                "generic": "Western Argentina Time",
			                "standard": "Western Argentina Standard Time",
			                "daylight": "Western Argentina Summer Time"
			              }
			            },
			            "Armenia": {
			              "long": {
			                "generic": "Armenia Time",
			                "standard": "Armenia Standard Time",
			                "daylight": "Armenia Summer Time"
			              }
			            },
			            "Atlantic": {
			              "long": {
			                "generic": "Atlantic Time",
			                "standard": "Atlantic Standard Time",
			                "daylight": "Atlantic Daylight Time"
			              },
			              "short": {
			                "generic": "AT",
			                "standard": "AST",
			                "daylight": "ADT"
			              }
			            },
			            "Australia_Central": {
			              "long": {
			                "generic": "Central Australia Time",
			                "standard": "Australian Central Standard Time",
			                "daylight": "Australian Central Daylight Time"
			              }
			            },
			            "Australia_CentralWestern": {
			              "long": {
			                "generic": "Australian Central Western Time",
			                "standard": "Australian Central Western Standard Time",
			                "daylight": "Australian Central Western Daylight Time"
			              }
			            },
			            "Australia_Eastern": {
			              "long": {
			                "generic": "Eastern Australia Time",
			                "standard": "Australian Eastern Standard Time",
			                "daylight": "Australian Eastern Daylight Time"
			              }
			            },
			            "Australia_Western": {
			              "long": {
			                "generic": "Western Australia Time",
			                "standard": "Australian Western Standard Time",
			                "daylight": "Australian Western Daylight Time"
			              }
			            },
			            "Azerbaijan": {
			              "long": {
			                "generic": "Azerbaijan Time",
			                "standard": "Azerbaijan Standard Time",
			                "daylight": "Azerbaijan Summer Time"
			              }
			            },
			            "Azores": {
			              "long": {
			                "generic": "Azores Time",
			                "standard": "Azores Standard Time",
			                "daylight": "Azores Summer Time"
			              }
			            },
			            "Bangladesh": {
			              "long": {
			                "generic": "Bangladesh Time",
			                "standard": "Bangladesh Standard Time",
			                "daylight": "Bangladesh Summer Time"
			              }
			            },
			            "Bhutan": {
			              "long": {
			                "standard": "Bhutan Time"
			              }
			            },
			            "Bolivia": {
			              "long": {
			                "standard": "Bolivia Time"
			              }
			            },
			            "Brasilia": {
			              "long": {
			                "generic": "Brasilia Time",
			                "standard": "Brasilia Standard Time",
			                "daylight": "Brasilia Summer Time"
			              }
			            },
			            "Brunei": {
			              "long": {
			                "standard": "Brunei Darussalam Time"
			              }
			            },
			            "Cape_Verde": {
			              "long": {
			                "generic": "Cape Verde Time",
			                "standard": "Cape Verde Standard Time",
			                "daylight": "Cape Verde Summer Time"
			              }
			            },
			            "Casey": {
			              "long": {
			                "standard": "Casey Time"
			              }
			            },
			            "Chamorro": {
			              "long": {
			                "standard": "Chamorro Standard Time"
			              }
			            },
			            "Chatham": {
			              "long": {
			                "generic": "Chatham Time",
			                "standard": "Chatham Standard Time",
			                "daylight": "Chatham Daylight Time"
			              }
			            },
			            "Chile": {
			              "long": {
			                "generic": "Chile Time",
			                "standard": "Chile Standard Time",
			                "daylight": "Chile Summer Time"
			              }
			            },
			            "China": {
			              "long": {
			                "generic": "China Time",
			                "standard": "China Standard Time",
			                "daylight": "China Daylight Time"
			              }
			            },
			            "Choibalsan": {
			              "long": {
			                "generic": "Choibalsan Time",
			                "standard": "Choibalsan Standard Time",
			                "daylight": "Choibalsan Summer Time"
			              }
			            },
			            "Christmas": {
			              "long": {
			                "standard": "Christmas Island Time"
			              }
			            },
			            "Cocos": {
			              "long": {
			                "standard": "Cocos Islands Time"
			              }
			            },
			            "Colombia": {
			              "long": {
			                "generic": "Colombia Time",
			                "standard": "Colombia Standard Time",
			                "daylight": "Colombia Summer Time"
			              }
			            },
			            "Cook": {
			              "long": {
			                "generic": "Cook Islands Time",
			                "standard": "Cook Islands Standard Time",
			                "daylight": "Cook Islands Half Summer Time"
			              }
			            },
			            "Cuba": {
			              "long": {
			                "generic": "Cuba Time",
			                "standard": "Cuba Standard Time",
			                "daylight": "Cuba Daylight Time"
			              }
			            },
			            "Davis": {
			              "long": {
			                "standard": "Davis Time"
			              }
			            },
			            "DumontDUrville": {
			              "long": {
			                "standard": "Dumont-d’Urville Time"
			              }
			            },
			            "East_Timor": {
			              "long": {
			                "standard": "East Timor Time"
			              }
			            },
			            "Easter": {
			              "long": {
			                "generic": "Easter Island Time",
			                "standard": "Easter Island Standard Time",
			                "daylight": "Easter Island Summer Time"
			              }
			            },
			            "Ecuador": {
			              "long": {
			                "standard": "Ecuador Time"
			              }
			            },
			            "Europe_Central": {
			              "long": {
			                "generic": "Central European Time",
			                "standard": "Central European Standard Time",
			                "daylight": "Central European Summer Time"
			              }
			            },
			            "Europe_Eastern": {
			              "long": {
			                "generic": "Eastern European Time",
			                "standard": "Eastern European Standard Time",
			                "daylight": "Eastern European Summer Time"
			              }
			            },
			            "Europe_Further_Eastern": {
			              "long": {
			                "standard": "Further-eastern European Time"
			              }
			            },
			            "Europe_Western": {
			              "long": {
			                "generic": "Western European Time",
			                "standard": "Western European Standard Time",
			                "daylight": "Western European Summer Time"
			              }
			            },
			            "Falkland": {
			              "long": {
			                "generic": "Falkland Islands Time",
			                "standard": "Falkland Islands Standard Time",
			                "daylight": "Falkland Islands Summer Time"
			              }
			            },
			            "Fiji": {
			              "long": {
			                "generic": "Fiji Time",
			                "standard": "Fiji Standard Time",
			                "daylight": "Fiji Summer Time"
			              }
			            },
			            "French_Guiana": {
			              "long": {
			                "standard": "French Guiana Time"
			              }
			            },
			            "French_Southern": {
			              "long": {
			                "standard": "French Southern & Antarctic Time"
			              }
			            },
			            "GMT": {
			              "long": {
			                "standard": "Greenwich Mean Time"
			              },
			              "short": {
			                "standard": "GMT"
			              }
			            },
			            "Galapagos": {
			              "long": {
			                "standard": "Galapagos Time"
			              }
			            },
			            "Gambier": {
			              "long": {
			                "standard": "Gambier Time"
			              }
			            },
			            "Georgia": {
			              "long": {
			                "generic": "Georgia Time",
			                "standard": "Georgia Standard Time",
			                "daylight": "Georgia Summer Time"
			              }
			            },
			            "Gilbert_Islands": {
			              "long": {
			                "standard": "Gilbert Islands Time"
			              }
			            },
			            "Greenland_Eastern": {
			              "long": {
			                "generic": "East Greenland Time",
			                "standard": "East Greenland Standard Time",
			                "daylight": "East Greenland Summer Time"
			              }
			            },
			            "Greenland_Western": {
			              "long": {
			                "generic": "West Greenland Time",
			                "standard": "West Greenland Standard Time",
			                "daylight": "West Greenland Summer Time"
			              }
			            },
			            "Guam": {
			              "long": {
			                "standard": "Guam Standard Time"
			              }
			            },
			            "Gulf": {
			              "long": {
			                "standard": "Gulf Standard Time"
			              }
			            },
			            "Guyana": {
			              "long": {
			                "standard": "Guyana Time"
			              }
			            },
			            "Hawaii_Aleutian": {
			              "long": {
			                "generic": "Hawaii-Aleutian Time",
			                "standard": "Hawaii-Aleutian Standard Time",
			                "daylight": "Hawaii-Aleutian Daylight Time"
			              },
			              "short": {
			                "generic": "HAT",
			                "standard": "HAST",
			                "daylight": "HADT"
			              }
			            },
			            "Hong_Kong": {
			              "long": {
			                "generic": "Hong Kong Time",
			                "standard": "Hong Kong Standard Time",
			                "daylight": "Hong Kong Summer Time"
			              }
			            },
			            "Hovd": {
			              "long": {
			                "generic": "Hovd Time",
			                "standard": "Hovd Standard Time",
			                "daylight": "Hovd Summer Time"
			              }
			            },
			            "India": {
			              "long": {
			                "standard": "India Standard Time"
			              }
			            },
			            "Indian_Ocean": {
			              "long": {
			                "standard": "Indian Ocean Time"
			              }
			            },
			            "Indochina": {
			              "long": {
			                "standard": "Indochina Time"
			              }
			            },
			            "Indonesia_Central": {
			              "long": {
			                "standard": "Central Indonesia Time"
			              }
			            },
			            "Indonesia_Eastern": {
			              "long": {
			                "standard": "Eastern Indonesia Time"
			              }
			            },
			            "Indonesia_Western": {
			              "long": {
			                "standard": "Western Indonesia Time"
			              }
			            },
			            "Iran": {
			              "long": {
			                "generic": "Iran Time",
			                "standard": "Iran Standard Time",
			                "daylight": "Iran Daylight Time"
			              }
			            },
			            "Irkutsk": {
			              "long": {
			                "generic": "Irkutsk Time",
			                "standard": "Irkutsk Standard Time",
			                "daylight": "Irkutsk Summer Time"
			              }
			            },
			            "Israel": {
			              "long": {
			                "generic": "Israel Time",
			                "standard": "Israel Standard Time",
			                "daylight": "Israel Daylight Time"
			              }
			            },
			            "Japan": {
			              "long": {
			                "generic": "Japan Time",
			                "standard": "Japan Standard Time",
			                "daylight": "Japan Daylight Time"
			              }
			            },
			            "Kamchatka": {
			              "long": {
			                "generic": "Petropavlovsk-Kamchatski Time",
			                "standard": "Petropavlovsk-Kamchatski Standard Time",
			                "daylight": "Petropavlovsk-Kamchatski Summer Time"
			              }
			            },
			            "Kazakhstan_Eastern": {
			              "long": {
			                "standard": "East Kazakhstan Time"
			              }
			            },
			            "Kazakhstan_Western": {
			              "long": {
			                "standard": "West Kazakhstan Time"
			              }
			            },
			            "Korea": {
			              "long": {
			                "generic": "Korean Time",
			                "standard": "Korean Standard Time",
			                "daylight": "Korean Daylight Time"
			              }
			            },
			            "Kosrae": {
			              "long": {
			                "standard": "Kosrae Time"
			              }
			            },
			            "Krasnoyarsk": {
			              "long": {
			                "generic": "Krasnoyarsk Time",
			                "standard": "Krasnoyarsk Standard Time",
			                "daylight": "Krasnoyarsk Summer Time"
			              }
			            },
			            "Kyrgystan": {
			              "long": {
			                "standard": "Kyrgystan Time"
			              }
			            },
			            "Lanka": {
			              "long": {
			                "standard": "Lanka Time"
			              }
			            },
			            "Line_Islands": {
			              "long": {
			                "standard": "Line Islands Time"
			              }
			            },
			            "Lord_Howe": {
			              "long": {
			                "generic": "Lord Howe Time",
			                "standard": "Lord Howe Standard Time",
			                "daylight": "Lord Howe Daylight Time"
			              }
			            },
			            "Macau": {
			              "long": {
			                "generic": "Macau Time",
			                "standard": "Macau Standard Time",
			                "daylight": "Macau Summer Time"
			              }
			            },
			            "Macquarie": {
			              "long": {
			                "standard": "Macquarie Island Time"
			              }
			            },
			            "Magadan": {
			              "long": {
			                "generic": "Magadan Time",
			                "standard": "Magadan Standard Time",
			                "daylight": "Magadan Summer Time"
			              }
			            },
			            "Malaysia": {
			              "long": {
			                "standard": "Malaysia Time"
			              }
			            },
			            "Maldives": {
			              "long": {
			                "standard": "Maldives Time"
			              }
			            },
			            "Marquesas": {
			              "long": {
			                "standard": "Marquesas Time"
			              }
			            },
			            "Marshall_Islands": {
			              "long": {
			                "standard": "Marshall Islands Time"
			              }
			            },
			            "Mauritius": {
			              "long": {
			                "generic": "Mauritius Time",
			                "standard": "Mauritius Standard Time",
			                "daylight": "Mauritius Summer Time"
			              }
			            },
			            "Mawson": {
			              "long": {
			                "standard": "Mawson Time"
			              }
			            },
			            "Mexico_Northwest": {
			              "long": {
			                "generic": "Northwest Mexico Time",
			                "standard": "Northwest Mexico Standard Time",
			                "daylight": "Northwest Mexico Daylight Time"
			              }
			            },
			            "Mexico_Pacific": {
			              "long": {
			                "generic": "Mexican Pacific Time",
			                "standard": "Mexican Pacific Standard Time",
			                "daylight": "Mexican Pacific Daylight Time"
			              }
			            },
			            "Mongolia": {
			              "long": {
			                "generic": "Ulan Bator Time",
			                "standard": "Ulan Bator Standard Time",
			                "daylight": "Ulan Bator Summer Time"
			              }
			            },
			            "Moscow": {
			              "long": {
			                "generic": "Moscow Time",
			                "standard": "Moscow Standard Time",
			                "daylight": "Moscow Summer Time"
			              }
			            },
			            "Myanmar": {
			              "long": {
			                "standard": "Myanmar Time"
			              }
			            },
			            "Nauru": {
			              "long": {
			                "standard": "Nauru Time"
			              }
			            },
			            "Nepal": {
			              "long": {
			                "standard": "Nepal Time"
			              }
			            },
			            "New_Caledonia": {
			              "long": {
			                "generic": "New Caledonia Time",
			                "standard": "New Caledonia Standard Time",
			                "daylight": "New Caledonia Summer Time"
			              }
			            },
			            "New_Zealand": {
			              "long": {
			                "generic": "New Zealand Time",
			                "standard": "New Zealand Standard Time",
			                "daylight": "New Zealand Daylight Time"
			              }
			            },
			            "Newfoundland": {
			              "long": {
			                "generic": "Newfoundland Time",
			                "standard": "Newfoundland Standard Time",
			                "daylight": "Newfoundland Daylight Time"
			              }
			            },
			            "Niue": {
			              "long": {
			                "standard": "Niue Time"
			              }
			            },
			            "Norfolk": {
			              "long": {
			                "standard": "Norfolk Island Time"
			              }
			            },
			            "Noronha": {
			              "long": {
			                "generic": "Fernando de Noronha Time",
			                "standard": "Fernando de Noronha Standard Time",
			                "daylight": "Fernando de Noronha Summer Time"
			              }
			            },
			            "North_Mariana": {
			              "long": {
			                "standard": "North Mariana Islands Time"
			              }
			            },
			            "Novosibirsk": {
			              "long": {
			                "generic": "Novosibirsk Time",
			                "standard": "Novosibirsk Standard Time",
			                "daylight": "Novosibirsk Summer Time"
			              }
			            },
			            "Omsk": {
			              "long": {
			                "generic": "Omsk Time",
			                "standard": "Omsk Standard Time",
			                "daylight": "Omsk Summer Time"
			              }
			            },
			            "Pakistan": {
			              "long": {
			                "generic": "Pakistan Time",
			                "standard": "Pakistan Standard Time",
			                "daylight": "Pakistan Summer Time"
			              }
			            },
			            "Palau": {
			              "long": {
			                "standard": "Palau Time"
			              }
			            },
			            "Papua_New_Guinea": {
			              "long": {
			                "standard": "Papua New Guinea Time"
			              }
			            },
			            "Paraguay": {
			              "long": {
			                "generic": "Paraguay Time",
			                "standard": "Paraguay Standard Time",
			                "daylight": "Paraguay Summer Time"
			              }
			            },
			            "Peru": {
			              "long": {
			                "generic": "Peru Time",
			                "standard": "Peru Standard Time",
			                "daylight": "Peru Summer Time"
			              }
			            },
			            "Philippines": {
			              "long": {
			                "generic": "Philippine Time",
			                "standard": "Philippine Standard Time",
			                "daylight": "Philippine Summer Time"
			              }
			            },
			            "Phoenix_Islands": {
			              "long": {
			                "standard": "Phoenix Islands Time"
			              }
			            },
			            "Pierre_Miquelon": {
			              "long": {
			                "generic": "St. Pierre & Miquelon Time",
			                "standard": "St. Pierre & Miquelon Standard Time",
			                "daylight": "St. Pierre & Miquelon Daylight Time"
			              }
			            },
			            "Pitcairn": {
			              "long": {
			                "standard": "Pitcairn Time"
			              }
			            },
			            "Ponape": {
			              "long": {
			                "standard": "Ponape Time"
			              }
			            },
			            "Qyzylorda": {
			              "long": {
			                "generic": "Qyzylorda Time",
			                "standard": "Qyzylorda Standard Time",
			                "daylight": "Qyzylorda Summer Time"
			              }
			            },
			            "Reunion": {
			              "long": {
			                "standard": "Reunion Time"
			              }
			            },
			            "Rothera": {
			              "long": {
			                "standard": "Rothera Time"
			              }
			            },
			            "Sakhalin": {
			              "long": {
			                "generic": "Sakhalin Time",
			                "standard": "Sakhalin Standard Time",
			                "daylight": "Sakhalin Summer Time"
			              }
			            },
			            "Samara": {
			              "long": {
			                "generic": "Samara Time",
			                "standard": "Samara Standard Time",
			                "daylight": "Samara Summer Time"
			              }
			            },
			            "Samoa": {
			              "long": {
			                "generic": "Samoa Time",
			                "standard": "Samoa Standard Time",
			                "daylight": "Samoa Daylight Time"
			              }
			            },
			            "Seychelles": {
			              "long": {
			                "standard": "Seychelles Time"
			              }
			            },
			            "Singapore": {
			              "long": {
			                "standard": "Singapore Standard Time"
			              }
			            },
			            "Solomon": {
			              "long": {
			                "standard": "Solomon Islands Time"
			              }
			            },
			            "South_Georgia": {
			              "long": {
			                "standard": "South Georgia Time"
			              }
			            },
			            "Suriname": {
			              "long": {
			                "standard": "Suriname Time"
			              }
			            },
			            "Syowa": {
			              "long": {
			                "standard": "Syowa Time"
			              }
			            },
			            "Tahiti": {
			              "long": {
			                "standard": "Tahiti Time"
			              }
			            },
			            "Taipei": {
			              "long": {
			                "generic": "Taipei Time",
			                "standard": "Taipei Standard Time",
			                "daylight": "Taipei Daylight Time"
			              }
			            },
			            "Tajikistan": {
			              "long": {
			                "standard": "Tajikistan Time"
			              }
			            },
			            "Tokelau": {
			              "long": {
			                "standard": "Tokelau Time"
			              }
			            },
			            "Tonga": {
			              "long": {
			                "generic": "Tonga Time",
			                "standard": "Tonga Standard Time",
			                "daylight": "Tonga Summer Time"
			              }
			            },
			            "Truk": {
			              "long": {
			                "standard": "Chuuk Time"
			              }
			            },
			            "Turkmenistan": {
			              "long": {
			                "generic": "Turkmenistan Time",
			                "standard": "Turkmenistan Standard Time",
			                "daylight": "Turkmenistan Summer Time"
			              }
			            },
			            "Tuvalu": {
			              "long": {
			                "standard": "Tuvalu Time"
			              }
			            },
			            "Uruguay": {
			              "long": {
			                "generic": "Uruguay Time",
			                "standard": "Uruguay Standard Time",
			                "daylight": "Uruguay Summer Time"
			              }
			            },
			            "Uzbekistan": {
			              "long": {
			                "generic": "Uzbekistan Time",
			                "standard": "Uzbekistan Standard Time",
			                "daylight": "Uzbekistan Summer Time"
			              }
			            },
			            "Vanuatu": {
			              "long": {
			                "generic": "Vanuatu Time",
			                "standard": "Vanuatu Standard Time",
			                "daylight": "Vanuatu Summer Time"
			              }
			            },
			            "Venezuela": {
			              "long": {
			                "standard": "Venezuela Time"
			              }
			            },
			            "Vladivostok": {
			              "long": {
			                "generic": "Vladivostok Time",
			                "standard": "Vladivostok Standard Time",
			                "daylight": "Vladivostok Summer Time"
			              }
			            },
			            "Volgograd": {
			              "long": {
			                "generic": "Volgograd Time",
			                "standard": "Volgograd Standard Time",
			                "daylight": "Volgograd Summer Time"
			              }
			            },
			            "Vostok": {
			              "long": {
			                "standard": "Vostok Time"
			              }
			            },
			            "Wake": {
			              "long": {
			                "standard": "Wake Island Time"
			              }
			            },
			            "Wallis": {
			              "long": {
			                "standard": "Wallis & Futuna Time"
			              }
			            },
			            "Yakutsk": {
			              "long": {
			                "generic": "Yakutsk Time",
			                "standard": "Yakutsk Standard Time",
			                "daylight": "Yakutsk Summer Time"
			              }
			            },
			            "Yekaterinburg": {
			              "long": {
			                "generic": "Yekaterinburg Time",
			                "standard": "Yekaterinburg Standard Time",
			                "daylight": "Yekaterinburg Summer Time"
			              }
			            }
			          }
			        }
			      }
			    }
			  }
			};

var fakeSupplementalTimeDataCatalog = {
		  "supplemental": {
			    "version": {
			      "_cldrVersion": "26",
			      "_number": "$Revision: 10969 $"
			    },
			    "generation": {
			      "_date": "$Date: 2014-09-11 12:17:53 -0500 (Thu, 11 Sep 2014) $"
			    },
			    "timeData": {
			      "001": {
			        "_allowed": "H h",
			        "_preferred": "H"
			      },
			      "AD": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "AE": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "AG": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "AL": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "AM": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "AO": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "AS": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "AT": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "AU": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "AW": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "AX": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "BB": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "BD": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "BE": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "BF": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "BH": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "BJ": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "BL": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "BM": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "BN": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "BQ": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "BR": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "BS": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "BT": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "BW": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "CA": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "CD": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "CG": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "CI": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "CN": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "CO": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "CP": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "CV": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "CY": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "CZ": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "DE": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "DJ": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "DK": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "DM": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "DZ": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "EE": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "EG": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "EH": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "ER": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "ET": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "FI": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "FJ": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "FM": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "FR": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "GA": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "GD": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "GF": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "GH": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "GL": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "GM": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "GN": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "GP": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "GR": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "GU": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "GW": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "GY": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "HK": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "HR": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "ID": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "IL": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "IN": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "IQ": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "IS": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "IT": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "JM": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "JO": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "JP": {
			        "_allowed": "H K h",
			        "_preferred": "H"
			      },
			      "KH": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "KI": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "KN": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "KP": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "KR": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "KW": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "KY": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "LB": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "LC": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "LR": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "LS": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "LY": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "MA": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "MC": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "MD": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "MF": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "MH": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "ML": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "MO": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "MP": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "MQ": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "MR": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "MW": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "MY": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "MZ": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "NA": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "NC": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "NE": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "NG": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "NL": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "NZ": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "OM": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "PG": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "PK": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "PM": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "PR": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "PS": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "PT": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "PW": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "QA": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "RE": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "RO": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "RU": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "SA": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "SB": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "SD": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "SE": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "SG": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "SI": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "SJ": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "SK": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "SL": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "SM": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "SO": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "SR": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "SS": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "ST": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "SY": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "SZ": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "TC": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "TD": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "TG": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "TN": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "TR": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "TT": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "TW": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "UM": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "US": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "VC": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "VG": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "VI": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "VU": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "WF": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "WS": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "YE": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "YT": {
			        "_allowed": "H",
			        "_preferred": "H"
			      },
			      "ZA": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "ZM": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      },
			      "ZW": {
			        "_allowed": "H h",
			        "_preferred": "h"
			      }
			    }
			  }
			};

var fakeSupplementalWeekDataCatalog = {
		  "supplemental": {
			    "version": {
			      "_cldrVersion": "26",
			      "_number": "$Revision: 10969 $"
			    },
			    "generation": {
			      "_date": "$Date: 2014-09-11 12:17:53 -0500 (Thu, 11 Sep 2014) $"
			    },
			    "weekData": {
			      "minDays": {
			        "001": "1",
			        "GU": "1",
			        "UM": "1",
			        "US": "1",
			        "VI": "1",
			        "AD": "4",
			        "AN": "4",
			        "AT": "4",
			        "AX": "4",
			        "BE": "4",
			        "BG": "4",
			        "CH": "4",
			        "CZ": "4",
			        "DE": "4",
			        "DK": "4",
			        "EE": "4",
			        "ES": "4",
			        "FI": "4",
			        "FJ": "4",
			        "FO": "4",
			        "FR": "4",
			        "GB": "4",
			        "GF": "4",
			        "GG": "4",
			        "GI": "4",
			        "GP": "4",
			        "GR": "4",
			        "HU": "4",
			        "IE": "4",
			        "IM": "4",
			        "IS": "4",
			        "IT": "4",
			        "JE": "4",
			        "LI": "4",
			        "LT": "4",
			        "LU": "4",
			        "MC": "4",
			        "MQ": "4",
			        "NL": "4",
			        "NO": "4",
			        "PL": "4",
			        "PT": "4",
			        "RE": "4",
			        "SE": "4",
			        "SJ": "4",
			        "SK": "4",
			        "SM": "4",
			        "VA": "4"
			      },
			      "firstDay": {
			        "001": "mon",
			        "AD": "mon",
			        "AI": "mon",
			        "AL": "mon",
			        "AM": "mon",
			        "AN": "mon",
			        "AT": "mon",
			        "AX": "mon",
			        "AZ": "mon",
			        "BA": "mon",
			        "BE": "mon",
			        "BG": "mon",
			        "BM": "mon",
			        "BN": "mon",
			        "BY": "mon",
			        "CH": "mon",
			        "CL": "mon",
			        "CM": "mon",
			        "CR": "mon",
			        "CY": "mon",
			        "CZ": "mon",
			        "DE": "mon",
			        "DK": "mon",
			        "EC": "mon",
			        "EE": "mon",
			        "ES": "mon",
			        "FI": "mon",
			        "FJ": "mon",
			        "FO": "mon",
			        "FR": "mon",
			        "GB": "mon",
			        "GE": "mon",
			        "GF": "mon",
			        "GP": "mon",
			        "GR": "mon",
			        "HR": "mon",
			        "HU": "mon",
			        "IS": "mon",
			        "IT": "mon",
			        "KG": "mon",
			        "KZ": "mon",
			        "LB": "mon",
			        "LI": "mon",
			        "LK": "mon",
			        "LT": "mon",
			        "LU": "mon",
			        "LV": "mon",
			        "MC": "mon",
			        "MD": "mon",
			        "ME": "mon",
			        "MK": "mon",
			        "MN": "mon",
			        "MQ": "mon",
			        "MY": "mon",
			        "NL": "mon",
			        "NO": "mon",
			        "PL": "mon",
			        "PT": "mon",
			        "RE": "mon",
			        "RO": "mon",
			        "RS": "mon",
			        "RU": "mon",
			        "SE": "mon",
			        "SI": "mon",
			        "SK": "mon",
			        "SM": "mon",
			        "TJ": "mon",
			        "TM": "mon",
			        "TR": "mon",
			        "UA": "mon",
			        "UY": "mon",
			        "UZ": "mon",
			        "VA": "mon",
			        "VN": "mon",
			        "XK": "mon",
			        "AE": "sat",
			        "AF": "sat",
			        "BH": "sat",
			        "DJ": "sat",
			        "DZ": "sat",
			        "EG": "sat",
			        "IQ": "sat",
			        "IR": "sat",
			        "JO": "sat",
			        "KW": "sat",
			        "LY": "sat",
			        "MA": "sat",
			        "OM": "sat",
			        "QA": "sat",
			        "SD": "sat",
			        "SY": "sat",
			        "AG": "sun",
			        "AR": "sun",
			        "AS": "sun",
			        "AU": "sun",
			        "BR": "sun",
			        "BS": "sun",
			        "BT": "sun",
			        "BW": "sun",
			        "BZ": "sun",
			        "CA": "sun",
			        "CN": "sun",
			        "CO": "sun",
			        "DM": "sun",
			        "DO": "sun",
			        "ET": "sun",
			        "GT": "sun",
			        "GU": "sun",
			        "HK": "sun",
			        "HN": "sun",
			        "ID": "sun",
			        "IE": "sun",
			        "IL": "sun",
			        "IN": "sun",
			        "JM": "sun",
			        "JP": "sun",
			        "KE": "sun",
			        "KH": "sun",
			        "KR": "sun",
			        "LA": "sun",
			        "MH": "sun",
			        "MM": "sun",
			        "MO": "sun",
			        "MT": "sun",
			        "MX": "sun",
			        "MZ": "sun",
			        "NI": "sun",
			        "NP": "sun",
			        "NZ": "sun",
			        "PA": "sun",
			        "PE": "sun",
			        "PH": "sun",
			        "PK": "sun",
			        "PR": "sun",
			        "PY": "sun",
			        "SA": "sun",
			        "SG": "sun",
			        "SV": "sun",
			        "TH": "sun",
			        "TN": "sun",
			        "TT": "sun",
			        "TW": "sun",
			        "UM": "sun",
			        "US": "sun",
			        "VE": "sun",
			        "VI": "sun",
			        "WS": "sun",
			        "YE": "sun",
			        "ZA": "sun",
			        "ZW": "sun",
			        "BD": "fri",
			        "MV": "fri"
			      },
			      "firstDay-alt-variant": {
			        "GB": "sun"
			      },
			      "weekendStart": {
			        "001": "sat",
			        "AE": "fri",
			        "BH": "fri",
			        "DZ": "fri",
			        "EG": "fri",
			        "IL": "fri",
			        "IQ": "fri",
			        "IR": "fri",
			        "JO": "fri",
			        "KW": "fri",
			        "LY": "fri",
			        "MA": "fri",
			        "OM": "fri",
			        "QA": "fri",
			        "SA": "fri",
			        "SD": "fri",
			        "SY": "fri",
			        "TN": "fri",
			        "YE": "fri",
			        "AF": "thu",
			        "IN": "sun"
			      },
			      "weekendEnd": {
			        "001": "sun",
			        "AE": "sat",
			        "BH": "sat",
			        "DZ": "sat",
			        "EG": "sat",
			        "IL": "sat",
			        "IQ": "sat",
			        "JO": "sat",
			        "KW": "sat",
			        "LY": "sat",
			        "MA": "sat",
			        "OM": "sat",
			        "QA": "sat",
			        "SA": "sat",
			        "SD": "sat",
			        "SY": "sat",
			        "TN": "sat",
			        "YE": "sat",
			        "AF": "fri",
			        "IR": "fri"
			      }
			    }
			  }
			};
	
var fakeEnCurrenciesCatalog = {
		  "main": {
			    "en-US": {
			      "identity": {
			        "version": {
			          "_cldrVersion": "26",
			          "_number": "$Revision: 10669 $"
			        },
			        "generation": {
			          "_date": "$Date: 2014-07-23 16:10:33 -0500 (Wed, 23 Jul 2014) $"
			        },
			        "language": "en",
			        "territory": "US"
			      },
			      "numbers": {
			        "currencies": {
			          "ADP": {
			            "displayName": "Andorran Peseta",
			            "displayName-count-one": "Andorran peseta",
			            "displayName-count-other": "Andorran pesetas",
			            "symbol": "ADP"
			          },
			          "AED": {
			            "displayName": "United Arab Emirates Dirham",
			            "displayName-count-one": "UAE dirham",
			            "displayName-count-other": "UAE dirhams",
			            "symbol": "AED"
			          },
			          "AFA": {
			            "displayName": "Afghan Afghani (1927–2002)",
			            "displayName-count-one": "Afghan afghani (1927–2002)",
			            "displayName-count-other": "Afghan afghanis (1927–2002)",
			            "symbol": "AFA"
			          },
			          "AFN": {
			            "displayName": "Afghan Afghani",
			            "displayName-count-one": "Afghan Afghani",
			            "displayName-count-other": "Afghan Afghanis",
			            "symbol": "AFN"
			          },
			          "ALK": {
			            "displayName": "Albanian Lek (1946–1965)",
			            "displayName-count-one": "Albanian lek (1946–1965)",
			            "displayName-count-other": "Albanian lekë (1946–1965)"
			          },
			          "ALL": {
			            "displayName": "Albanian Lek",
			            "displayName-count-one": "Albanian lek",
			            "displayName-count-other": "Albanian lekë",
			            "symbol": "ALL"
			          },
			          "AMD": {
			            "displayName": "Armenian Dram",
			            "displayName-count-one": "Armenian dram",
			            "displayName-count-other": "Armenian drams",
			            "symbol": "AMD"
			          },
			          "ANG": {
			            "displayName": "Netherlands Antillean Guilder",
			            "displayName-count-one": "Netherlands Antillean guilder",
			            "displayName-count-other": "Netherlands Antillean guilders",
			            "symbol": "ANG"
			          },
			          "AOA": {
			            "displayName": "Angolan Kwanza",
			            "displayName-count-one": "Angolan kwanza",
			            "displayName-count-other": "Angolan kwanzas",
			            "symbol": "AOA",
			            "symbol-alt-narrow": "Kz"
			          },
			          "AOK": {
			            "displayName": "Angolan Kwanza (1977–1991)",
			            "displayName-count-one": "Angolan kwanza (1977–1991)",
			            "displayName-count-other": "Angolan kwanzas (1977–1991)",
			            "symbol": "AOK"
			          },
			          "AON": {
			            "displayName": "Angolan New Kwanza (1990–2000)",
			            "displayName-count-one": "Angolan new kwanza (1990–2000)",
			            "displayName-count-other": "Angolan new kwanzas (1990–2000)",
			            "symbol": "AON"
			          },
			          "AOR": {
			            "displayName": "Angolan Readjusted Kwanza (1995–1999)",
			            "displayName-count-one": "Angolan readjusted kwanza (1995–1999)",
			            "displayName-count-other": "Angolan readjusted kwanzas (1995–1999)",
			            "symbol": "AOR"
			          },
			          "ARA": {
			            "displayName": "Argentine Austral",
			            "displayName-count-one": "Argentine austral",
			            "displayName-count-other": "Argentine australs",
			            "symbol": "ARA"
			          },
			          "ARL": {
			            "displayName": "Argentine Peso Ley (1970–1983)",
			            "displayName-count-one": "Argentine peso ley (1970–1983)",
			            "displayName-count-other": "Argentine pesos ley (1970–1983)",
			            "symbol": "ARL"
			          },
			          "ARM": {
			            "displayName": "Argentine Peso (1881–1970)",
			            "displayName-count-one": "Argentine peso (1881–1970)",
			            "displayName-count-other": "Argentine pesos (1881–1970)",
			            "symbol": "ARM"
			          },
			          "ARP": {
			            "displayName": "Argentine Peso (1983–1985)",
			            "displayName-count-one": "Argentine peso (1983–1985)",
			            "displayName-count-other": "Argentine pesos (1983–1985)",
			            "symbol": "ARP"
			          },
			          "ARS": {
			            "displayName": "Argentine Peso",
			            "displayName-count-one": "Argentine peso",
			            "displayName-count-other": "Argentine pesos",
			            "symbol": "ARS",
			            "symbol-alt-narrow": "$"
			          },
			          "ATS": {
			            "displayName": "Austrian Schilling",
			            "displayName-count-one": "Austrian schilling",
			            "displayName-count-other": "Austrian schillings",
			            "symbol": "ATS"
			          },
			          "AUD": {
			            "displayName": "Australian Dollar",
			            "displayName-count-one": "Australian dollar",
			            "displayName-count-other": "Australian dollars",
			            "symbol": "A$",
			            "symbol-alt-narrow": "$"
			          },
			          "AWG": {
			            "displayName": "Aruban Florin",
			            "displayName-count-one": "Aruban florin",
			            "displayName-count-other": "Aruban florin",
			            "symbol": "AWG"
			          },
			          "AZM": {
			            "displayName": "Azerbaijani Manat (1993–2006)",
			            "displayName-count-one": "Azerbaijani manat (1993–2006)",
			            "displayName-count-other": "Azerbaijani manats (1993–2006)",
			            "symbol": "AZM"
			          },
			          "AZN": {
			            "displayName": "Azerbaijani Manat",
			            "displayName-count-one": "Azerbaijani manat",
			            "displayName-count-other": "Azerbaijani manats",
			            "symbol": "AZN"
			          },
			          "BAD": {
			            "displayName": "Bosnia-Herzegovina Dinar (1992–1994)",
			            "displayName-count-one": "Bosnia-Herzegovina dinar (1992–1994)",
			            "displayName-count-other": "Bosnia-Herzegovina dinars (1992–1994)",
			            "symbol": "BAD"
			          },
			          "BAM": {
			            "displayName": "Bosnia-Herzegovina Convertible Mark",
			            "displayName-count-one": "Bosnia-Herzegovina convertible mark",
			            "displayName-count-other": "Bosnia-Herzegovina convertible marks",
			            "symbol": "BAM",
			            "symbol-alt-narrow": "KM"
			          },
			          "BAN": {
			            "displayName": "Bosnia-Herzegovina New Dinar (1994–1997)",
			            "displayName-count-one": "Bosnia-Herzegovina new dinar (1994–1997)",
			            "displayName-count-other": "Bosnia-Herzegovina new dinars (1994–1997)",
			            "symbol": "BAN"
			          },
			          "BBD": {
			            "displayName": "Barbadian Dollar",
			            "displayName-count-one": "Barbadian dollar",
			            "displayName-count-other": "Barbadian dollars",
			            "symbol": "BBD",
			            "symbol-alt-narrow": "$"
			          },
			          "BDT": {
			            "displayName": "Bangladeshi Taka",
			            "displayName-count-one": "Bangladeshi taka",
			            "displayName-count-other": "Bangladeshi takas",
			            "symbol": "BDT",
			            "symbol-alt-narrow": "৳"
			          },
			          "BEC": {
			            "displayName": "Belgian Franc (convertible)",
			            "displayName-count-one": "Belgian franc (convertible)",
			            "displayName-count-other": "Belgian francs (convertible)",
			            "symbol": "BEC"
			          },
			          "BEF": {
			            "displayName": "Belgian Franc",
			            "displayName-count-one": "Belgian franc",
			            "displayName-count-other": "Belgian francs",
			            "symbol": "BEF"
			          },
			          "BEL": {
			            "displayName": "Belgian Franc (financial)",
			            "displayName-count-one": "Belgian franc (financial)",
			            "displayName-count-other": "Belgian francs (financial)",
			            "symbol": "BEL"
			          },
			          "BGL": {
			            "displayName": "Bulgarian Hard Lev",
			            "displayName-count-one": "Bulgarian hard lev",
			            "displayName-count-other": "Bulgarian hard leva",
			            "symbol": "BGL"
			          },
			          "BGM": {
			            "displayName": "Bulgarian Socialist Lev",
			            "displayName-count-one": "Bulgarian socialist lev",
			            "displayName-count-other": "Bulgarian socialist leva",
			            "symbol": "BGM"
			          },
			          "BGN": {
			            "displayName": "Bulgarian Lev",
			            "displayName-count-one": "Bulgarian lev",
			            "displayName-count-other": "Bulgarian leva",
			            "symbol": "BGN"
			          },
			          "BGO": {
			            "displayName": "Bulgarian Lev (1879–1952)",
			            "displayName-count-one": "Bulgarian lev (1879–1952)",
			            "displayName-count-other": "Bulgarian leva (1879–1952)",
			            "symbol": "BGO"
			          },
			          "BHD": {
			            "displayName": "Bahraini Dinar",
			            "displayName-count-one": "Bahraini dinar",
			            "displayName-count-other": "Bahraini dinars",
			            "symbol": "BHD"
			          },
			          "BIF": {
			            "displayName": "Burundian Franc",
			            "displayName-count-one": "Burundian franc",
			            "displayName-count-other": "Burundian francs",
			            "symbol": "BIF"
			          },
			          "BMD": {
			            "displayName": "Bermudan Dollar",
			            "displayName-count-one": "Bermudan dollar",
			            "displayName-count-other": "Bermudan dollars",
			            "symbol": "BMD",
			            "symbol-alt-narrow": "$"
			          },
			          "BND": {
			            "displayName": "Brunei Dollar",
			            "displayName-count-one": "Brunei dollar",
			            "displayName-count-other": "Brunei dollars",
			            "symbol": "BND",
			            "symbol-alt-narrow": "$"
			          },
			          "BOB": {
			            "displayName": "Bolivian Boliviano",
			            "displayName-count-one": "Bolivian boliviano",
			            "displayName-count-other": "Bolivian bolivianos",
			            "symbol": "BOB",
			            "symbol-alt-narrow": "Bs"
			          },
			          "BOL": {
			            "displayName": "Bolivian Boliviano (1863–1963)",
			            "displayName-count-one": "Bolivian boliviano (1863–1963)",
			            "displayName-count-other": "Bolivian bolivianos (1863–1963)",
			            "symbol": "BOL"
			          },
			          "BOP": {
			            "displayName": "Bolivian Peso",
			            "displayName-count-one": "Bolivian peso",
			            "displayName-count-other": "Bolivian pesos",
			            "symbol": "BOP"
			          },
			          "BOV": {
			            "displayName": "Bolivian Mvdol",
			            "displayName-count-one": "Bolivian mvdol",
			            "displayName-count-other": "Bolivian mvdols",
			            "symbol": "BOV"
			          },
			          "BRB": {
			            "displayName": "Brazilian New Cruzeiro (1967–1986)",
			            "displayName-count-one": "Brazilian new cruzeiro (1967–1986)",
			            "displayName-count-other": "Brazilian new cruzeiros (1967–1986)",
			            "symbol": "BRB"
			          },
			          "BRC": {
			            "displayName": "Brazilian Cruzado (1986–1989)",
			            "displayName-count-one": "Brazilian cruzado (1986–1989)",
			            "displayName-count-other": "Brazilian cruzados (1986–1989)",
			            "symbol": "BRC"
			          },
			          "BRE": {
			            "displayName": "Brazilian Cruzeiro (1990–1993)",
			            "displayName-count-one": "Brazilian cruzeiro (1990–1993)",
			            "displayName-count-other": "Brazilian cruzeiros (1990–1993)",
			            "symbol": "BRE"
			          },
			          "BRL": {
			            "displayName": "Brazilian Real",
			            "displayName-count-one": "Brazilian real",
			            "displayName-count-other": "Brazilian reals",
			            "symbol": "R$",
			            "symbol-alt-narrow": "R$"
			          },
			          "BRN": {
			            "displayName": "Brazilian New Cruzado (1989–1990)",
			            "displayName-count-one": "Brazilian new cruzado (1989–1990)",
			            "displayName-count-other": "Brazilian new cruzados (1989–1990)",
			            "symbol": "BRN"
			          },
			          "BRR": {
			            "displayName": "Brazilian Cruzeiro (1993–1994)",
			            "displayName-count-one": "Brazilian cruzeiro (1993–1994)",
			            "displayName-count-other": "Brazilian cruzeiros (1993–1994)",
			            "symbol": "BRR"
			          },
			          "BRZ": {
			            "displayName": "Brazilian Cruzeiro (1942–1967)",
			            "displayName-count-one": "Brazilian cruzeiro (1942–1967)",
			            "displayName-count-other": "Brazilian cruzeiros (1942–1967)",
			            "symbol": "BRZ"
			          },
			          "BSD": {
			            "displayName": "Bahamian Dollar",
			            "displayName-count-one": "Bahamian dollar",
			            "displayName-count-other": "Bahamian dollars",
			            "symbol": "BSD",
			            "symbol-alt-narrow": "$"
			          },
			          "BTN": {
			            "displayName": "Bhutanese Ngultrum",
			            "displayName-count-one": "Bhutanese ngultrum",
			            "displayName-count-other": "Bhutanese ngultrums",
			            "symbol": "BTN"
			          },
			          "BUK": {
			            "displayName": "Burmese Kyat",
			            "displayName-count-one": "Burmese kyat",
			            "displayName-count-other": "Burmese kyats",
			            "symbol": "BUK"
			          },
			          "BWP": {
			            "displayName": "Botswanan Pula",
			            "displayName-count-one": "Botswanan pula",
			            "displayName-count-other": "Botswanan pulas",
			            "symbol": "BWP",
			            "symbol-alt-narrow": "P"
			          },
			          "BYB": {
			            "displayName": "Belarusian New Ruble (1994–1999)",
			            "displayName-count-one": "Belarusian new ruble (1994–1999)",
			            "displayName-count-other": "Belarusian new rubles (1994–1999)",
			            "symbol": "BYB"
			          },
			          "BYR": {
			            "displayName": "Belarusian Ruble",
			            "displayName-count-one": "Belarusian ruble",
			            "displayName-count-other": "Belarusian rubles",
			            "symbol": "BYR",
			            "symbol-alt-narrow": "р."
			          },
			          "BZD": {
			            "displayName": "Belize Dollar",
			            "displayName-count-one": "Belize dollar",
			            "displayName-count-other": "Belize dollars",
			            "symbol": "BZD",
			            "symbol-alt-narrow": "$"
			          },
			          "CAD": {
			            "displayName": "Canadian Dollar",
			            "displayName-count-one": "Canadian dollar",
			            "displayName-count-other": "Canadian dollars",
			            "symbol": "CA$",
			            "symbol-alt-narrow": "$"
			          },
			          "CDF": {
			            "displayName": "Congolese Franc",
			            "displayName-count-one": "Congolese franc",
			            "displayName-count-other": "Congolese francs",
			            "symbol": "CDF"
			          },
			          "CHE": {
			            "displayName": "WIR Euro",
			            "displayName-count-one": "WIR euro",
			            "displayName-count-other": "WIR euros",
			            "symbol": "CHE"
			          },
			          "CHF": {
			            "displayName": "Swiss Franc",
			            "displayName-count-one": "Swiss franc",
			            "displayName-count-other": "Swiss francs",
			            "symbol": "CHF"
			          },
			          "CHW": {
			            "displayName": "WIR Franc",
			            "displayName-count-one": "WIR franc",
			            "displayName-count-other": "WIR francs",
			            "symbol": "CHW"
			          },
			          "CLE": {
			            "displayName": "Chilean Escudo",
			            "displayName-count-one": "Chilean escudo",
			            "displayName-count-other": "Chilean escudos",
			            "symbol": "CLE"
			          },
			          "CLF": {
			            "displayName": "Chilean Unit of Account (UF)",
			            "displayName-count-one": "Chilean unit of account (UF)",
			            "displayName-count-other": "Chilean units of account (UF)",
			            "symbol": "CLF"
			          },
			          "CLP": {
			            "displayName": "Chilean Peso",
			            "displayName-count-one": "Chilean peso",
			            "displayName-count-other": "Chilean pesos",
			            "symbol": "CLP",
			            "symbol-alt-narrow": "$"
			          },
			          "CNX": {
			            "displayName": "Chinese People’s Bank Dollar",
			            "displayName-count-one": "Chinese People’s Bank dollar",
			            "displayName-count-other": "Chinese People’s Bank dollars"
			          },
			          "CNY": {
			            "displayName": "Chinese Yuan",
			            "displayName-count-one": "Chinese yuan",
			            "displayName-count-other": "Chinese yuan",
			            "symbol": "CN¥",
			            "symbol-alt-narrow": "¥"
			          },
			          "COP": {
			            "displayName": "Colombian Peso",
			            "displayName-count-one": "Colombian peso",
			            "displayName-count-other": "Colombian pesos",
			            "symbol": "COP",
			            "symbol-alt-narrow": "$"
			          },
			          "COU": {
			            "displayName": "Colombian Real Value Unit",
			            "displayName-count-one": "Colombian real value unit",
			            "displayName-count-other": "Colombian real value units",
			            "symbol": "COU"
			          },
			          "CRC": {
			            "displayName": "Costa Rican Colón",
			            "displayName-count-one": "Costa Rican colón",
			            "displayName-count-other": "Costa Rican colóns",
			            "symbol": "CRC",
			            "symbol-alt-narrow": "₡"
			          },
			          "CSD": {
			            "displayName": "Serbian Dinar (2002–2006)",
			            "displayName-count-one": "Serbian dinar (2002–2006)",
			            "displayName-count-other": "Serbian dinars (2002–2006)",
			            "symbol": "CSD"
			          },
			          "CSK": {
			            "displayName": "Czechoslovak Hard Koruna",
			            "displayName-count-one": "Czechoslovak hard koruna",
			            "displayName-count-other": "Czechoslovak hard korunas",
			            "symbol": "CSK"
			          },
			          "CUC": {
			            "displayName": "Cuban Convertible Peso",
			            "displayName-count-one": "Cuban convertible peso",
			            "displayName-count-other": "Cuban convertible pesos",
			            "symbol": "CUC",
			            "symbol-alt-narrow": "$"
			          },
			          "CUP": {
			            "displayName": "Cuban Peso",
			            "displayName-count-one": "Cuban peso",
			            "displayName-count-other": "Cuban pesos",
			            "symbol": "CUP",
			            "symbol-alt-narrow": "$"
			          },
			          "CVE": {
			            "displayName": "Cape Verdean Escudo",
			            "displayName-count-one": "Cape Verdean escudo",
			            "displayName-count-other": "Cape Verdean escudos",
			            "symbol": "CVE"
			          },
			          "CYP": {
			            "displayName": "Cypriot Pound",
			            "displayName-count-one": "Cypriot pound",
			            "displayName-count-other": "Cypriot pounds",
			            "symbol": "CYP"
			          },
			          "CZK": {
			            "displayName": "Czech Republic Koruna",
			            "displayName-count-one": "Czech Republic koruna",
			            "displayName-count-other": "Czech Republic korunas",
			            "symbol": "CZK",
			            "symbol-alt-narrow": "Kč"
			          },
			          "DDM": {
			            "displayName": "East German Mark",
			            "displayName-count-one": "East German mark",
			            "displayName-count-other": "East German marks",
			            "symbol": "DDM"
			          },
			          "DEM": {
			            "displayName": "German Mark",
			            "displayName-count-one": "German mark",
			            "displayName-count-other": "German marks",
			            "symbol": "DEM"
			          },
			          "DJF": {
			            "displayName": "Djiboutian Franc",
			            "displayName-count-one": "Djiboutian franc",
			            "displayName-count-other": "Djiboutian francs",
			            "symbol": "DJF"
			          },
			          "DKK": {
			            "displayName": "Danish Krone",
			            "displayName-count-one": "Danish krone",
			            "displayName-count-other": "Danish kroner",
			            "symbol": "DKK",
			            "symbol-alt-narrow": "kr"
			          },
			          "DOP": {
			            "displayName": "Dominican Peso",
			            "displayName-count-one": "Dominican peso",
			            "displayName-count-other": "Dominican pesos",
			            "symbol": "DOP",
			            "symbol-alt-narrow": "$"
			          },
			          "DZD": {
			            "displayName": "Algerian Dinar",
			            "displayName-count-one": "Algerian dinar",
			            "displayName-count-other": "Algerian dinars",
			            "symbol": "DZD"
			          },
			          "ECS": {
			            "displayName": "Ecuadorian Sucre",
			            "displayName-count-one": "Ecuadorian sucre",
			            "displayName-count-other": "Ecuadorian sucres",
			            "symbol": "ECS"
			          },
			          "ECV": {
			            "displayName": "Ecuadorian Unit of Constant Value",
			            "displayName-count-one": "Ecuadorian unit of constant value",
			            "displayName-count-other": "Ecuadorian units of constant value",
			            "symbol": "ECV"
			          },
			          "EEK": {
			            "displayName": "Estonian Kroon",
			            "displayName-count-one": "Estonian kroon",
			            "displayName-count-other": "Estonian kroons",
			            "symbol": "EEK"
			          },
			          "EGP": {
			            "displayName": "Egyptian Pound",
			            "displayName-count-one": "Egyptian pound",
			            "displayName-count-other": "Egyptian pounds",
			            "symbol": "EGP",
			            "symbol-alt-narrow": "E£"
			          },
			          "ERN": {
			            "displayName": "Eritrean Nakfa",
			            "displayName-count-one": "Eritrean nakfa",
			            "displayName-count-other": "Eritrean nakfas",
			            "symbol": "ERN"
			          },
			          "ESA": {
			            "displayName": "Spanish Peseta (A account)",
			            "displayName-count-one": "Spanish peseta (A account)",
			            "displayName-count-other": "Spanish pesetas (A account)",
			            "symbol": "ESA"
			          },
			          "ESB": {
			            "displayName": "Spanish Peseta (convertible account)",
			            "displayName-count-one": "Spanish peseta (convertible account)",
			            "displayName-count-other": "Spanish pesetas (convertible account)",
			            "symbol": "ESB"
			          },
			          "ESP": {
			            "displayName": "Spanish Peseta",
			            "displayName-count-one": "Spanish peseta",
			            "displayName-count-other": "Spanish pesetas",
			            "symbol": "ESP",
			            "symbol-alt-narrow": "₧"
			          },
			          "ETB": {
			            "displayName": "Ethiopian Birr",
			            "displayName-count-one": "Ethiopian birr",
			            "displayName-count-other": "Ethiopian birrs",
			            "symbol": "ETB"
			          },
			          "EUR": {
			            "displayName": "Euro",
			            "displayName-count-one": "euro",
			            "displayName-count-other": "euros",
			            "symbol": "€",
			            "symbol-alt-narrow": "€"
			          },
			          "FIM": {
			            "displayName": "Finnish Markka",
			            "displayName-count-one": "Finnish markka",
			            "displayName-count-other": "Finnish markkas",
			            "symbol": "FIM"
			          },
			          "FJD": {
			            "displayName": "Fijian Dollar",
			            "displayName-count-one": "Fijian dollar",
			            "displayName-count-other": "Fijian dollars",
			            "symbol": "FJD",
			            "symbol-alt-narrow": "$"
			          },
			          "FKP": {
			            "displayName": "Falkland Islands Pound",
			            "displayName-count-one": "Falkland Islands pound",
			            "displayName-count-other": "Falkland Islands pounds",
			            "symbol": "FKP",
			            "symbol-alt-narrow": "£"
			          },
			          "FRF": {
			            "displayName": "French Franc",
			            "displayName-count-one": "French franc",
			            "displayName-count-other": "French francs",
			            "symbol": "FRF"
			          },
			          "GBP": {
			            "displayName": "British Pound Sterling",
			            "displayName-count-one": "British pound sterling",
			            "displayName-count-other": "British pounds sterling",
			            "symbol": "£",
			            "symbol-alt-narrow": "£"
			          },
			          "GEK": {
			            "displayName": "Georgian Kupon Larit",
			            "displayName-count-one": "Georgian kupon larit",
			            "displayName-count-other": "Georgian kupon larits",
			            "symbol": "GEK"
			          },
			          "GEL": {
			            "displayName": "Georgian Lari",
			            "displayName-count-one": "Georgian lari",
			            "displayName-count-other": "Georgian laris",
			            "symbol": "GEL"
			          },
			          "GHC": {
			            "displayName": "Ghanaian Cedi (1979–2007)",
			            "displayName-count-one": "Ghanaian cedi (1979–2007)",
			            "displayName-count-other": "Ghanaian cedis (1979–2007)",
			            "symbol": "GHC"
			          },
			          "GHS": {
			            "displayName": "Ghanaian Cedi",
			            "displayName-count-one": "Ghanaian cedi",
			            "displayName-count-other": "Ghanaian cedis",
			            "symbol": "GHS"
			          },
			          "GIP": {
			            "displayName": "Gibraltar Pound",
			            "displayName-count-one": "Gibraltar pound",
			            "displayName-count-other": "Gibraltar pounds",
			            "symbol": "GIP",
			            "symbol-alt-narrow": "£"
			          },
			          "GMD": {
			            "displayName": "Gambian Dalasi",
			            "displayName-count-one": "Gambian dalasi",
			            "displayName-count-other": "Gambian dalasis",
			            "symbol": "GMD"
			          },
			          "GNF": {
			            "displayName": "Guinean Franc",
			            "displayName-count-one": "Guinean franc",
			            "displayName-count-other": "Guinean francs",
			            "symbol": "GNF",
			            "symbol-alt-narrow": "FG"
			          },
			          "GNS": {
			            "displayName": "Guinean Syli",
			            "displayName-count-one": "Guinean syli",
			            "displayName-count-other": "Guinean sylis",
			            "symbol": "GNS"
			          },
			          "GQE": {
			            "displayName": "Equatorial Guinean Ekwele",
			            "displayName-count-one": "Equatorial Guinean ekwele",
			            "displayName-count-other": "Equatorial Guinean ekwele",
			            "symbol": "GQE"
			          },
			          "GRD": {
			            "displayName": "Greek Drachma",
			            "displayName-count-one": "Greek drachma",
			            "displayName-count-other": "Greek drachmas",
			            "symbol": "GRD"
			          },
			          "GTQ": {
			            "displayName": "Guatemalan Quetzal",
			            "displayName-count-one": "Guatemalan quetzal",
			            "displayName-count-other": "Guatemalan quetzals",
			            "symbol": "GTQ",
			            "symbol-alt-narrow": "Q"
			          },
			          "GWE": {
			            "displayName": "Portuguese Guinea Escudo",
			            "displayName-count-one": "Portuguese Guinea escudo",
			            "displayName-count-other": "Portuguese Guinea escudos",
			            "symbol": "GWE"
			          },
			          "GWP": {
			            "displayName": "Guinea-Bissau Peso",
			            "displayName-count-one": "Guinea-Bissau peso",
			            "displayName-count-other": "Guinea-Bissau pesos",
			            "symbol": "GWP"
			          },
			          "GYD": {
			            "displayName": "Guyanaese Dollar",
			            "displayName-count-one": "Guyanaese dollar",
			            "displayName-count-other": "Guyanaese dollars",
			            "symbol": "GYD",
			            "symbol-alt-narrow": "$"
			          },
			          "HKD": {
			            "displayName": "Hong Kong Dollar",
			            "displayName-count-one": "Hong Kong dollar",
			            "displayName-count-other": "Hong Kong dollars",
			            "symbol": "HK$",
			            "symbol-alt-narrow": "$"
			          },
			          "HNL": {
			            "displayName": "Honduran Lempira",
			            "displayName-count-one": "Honduran lempira",
			            "displayName-count-other": "Honduran lempiras",
			            "symbol": "HNL",
			            "symbol-alt-narrow": "L"
			          },
			          "HRD": {
			            "displayName": "Croatian Dinar",
			            "displayName-count-one": "Croatian dinar",
			            "displayName-count-other": "Croatian dinars",
			            "symbol": "HRD"
			          },
			          "HRK": {
			            "displayName": "Croatian Kuna",
			            "displayName-count-one": "Croatian kuna",
			            "displayName-count-other": "Croatian kunas",
			            "symbol": "HRK",
			            "symbol-alt-narrow": "kn"
			          },
			          "HTG": {
			            "displayName": "Haitian Gourde",
			            "displayName-count-one": "Haitian gourde",
			            "displayName-count-other": "Haitian gourdes",
			            "symbol": "HTG"
			          },
			          "HUF": {
			            "displayName": "Hungarian Forint",
			            "displayName-count-one": "Hungarian forint",
			            "displayName-count-other": "Hungarian forints",
			            "symbol": "HUF",
			            "symbol-alt-narrow": "Ft"
			          },
			          "IDR": {
			            "displayName": "Indonesian Rupiah",
			            "displayName-count-one": "Indonesian rupiah",
			            "displayName-count-other": "Indonesian rupiahs",
			            "symbol": "IDR",
			            "symbol-alt-narrow": "Rp"
			          },
			          "IEP": {
			            "displayName": "Irish Pound",
			            "displayName-count-one": "Irish pound",
			            "displayName-count-other": "Irish pounds",
			            "symbol": "IEP"
			          },
			          "ILP": {
			            "displayName": "Israeli Pound",
			            "displayName-count-one": "Israeli pound",
			            "displayName-count-other": "Israeli pounds",
			            "symbol": "ILP"
			          },
			          "ILR": {
			            "displayName": "Israeli Sheqel (1980–1985)",
			            "displayName-count-one": "Israeli sheqel (1980–1985)",
			            "displayName-count-other": "Israeli sheqels (1980–1985)"
			          },
			          "ILS": {
			            "displayName": "Israeli New Sheqel",
			            "displayName-count-one": "Israeli new sheqel",
			            "displayName-count-other": "Israeli new sheqels",
			            "symbol": "₪",
			            "symbol-alt-narrow": "₪"
			          },
			          "INR": {
			            "displayName": "Indian Rupee",
			            "displayName-count-one": "Indian rupee",
			            "displayName-count-other": "Indian rupees",
			            "symbol": "₹",
			            "symbol-alt-narrow": "₹"
			          },
			          "IQD": {
			            "displayName": "Iraqi Dinar",
			            "displayName-count-one": "Iraqi dinar",
			            "displayName-count-other": "Iraqi dinars",
			            "symbol": "IQD"
			          },
			          "IRR": {
			            "displayName": "Iranian Rial",
			            "displayName-count-one": "Iranian rial",
			            "displayName-count-other": "Iranian rials",
			            "symbol": "IRR"
			          },
			          "ISJ": {
			            "displayName": "Icelandic Króna (1918–1981)",
			            "displayName-count-one": "Icelandic króna (1918–1981)",
			            "displayName-count-other": "Icelandic krónur (1918–1981)"
			          },
			          "ISK": {
			            "displayName": "Icelandic Króna",
			            "displayName-count-one": "Icelandic króna",
			            "displayName-count-other": "Icelandic krónur",
			            "symbol": "ISK",
			            "symbol-alt-narrow": "kr"
			          },
			          "ITL": {
			            "displayName": "Italian Lira",
			            "displayName-count-one": "Italian lira",
			            "displayName-count-other": "Italian liras",
			            "symbol": "ITL"
			          },
			          "JMD": {
			            "displayName": "Jamaican Dollar",
			            "displayName-count-one": "Jamaican dollar",
			            "displayName-count-other": "Jamaican dollars",
			            "symbol": "JMD",
			            "symbol-alt-narrow": "$"
			          },
			          "JOD": {
			            "displayName": "Jordanian Dinar",
			            "displayName-count-one": "Jordanian dinar",
			            "displayName-count-other": "Jordanian dinars",
			            "symbol": "JOD"
			          },
			          "JPY": {
			            "displayName": "Japanese Yen",
			            "displayName-count-one": "Japanese yen",
			            "displayName-count-other": "Japanese yen",
			            "symbol": "¥",
			            "symbol-alt-narrow": "¥"
			          },
			          "KES": {
			            "displayName": "Kenyan Shilling",
			            "displayName-count-one": "Kenyan shilling",
			            "displayName-count-other": "Kenyan shillings",
			            "symbol": "KES"
			          },
			          "KGS": {
			            "displayName": "Kyrgystani Som",
			            "displayName-count-one": "Kyrgystani som",
			            "displayName-count-other": "Kyrgystani soms",
			            "symbol": "KGS"
			          },
			          "KHR": {
			            "displayName": "Cambodian Riel",
			            "displayName-count-one": "Cambodian riel",
			            "displayName-count-other": "Cambodian riels",
			            "symbol": "KHR",
			            "symbol-alt-narrow": "៛"
			          },
			          "KMF": {
			            "displayName": "Comorian Franc",
			            "displayName-count-one": "Comorian franc",
			            "displayName-count-other": "Comorian francs",
			            "symbol": "KMF",
			            "symbol-alt-narrow": "CF"
			          },
			          "KPW": {
			            "displayName": "North Korean Won",
			            "displayName-count-one": "North Korean won",
			            "displayName-count-other": "North Korean won",
			            "symbol": "KPW",
			            "symbol-alt-narrow": "₩"
			          },
			          "KRH": {
			            "displayName": "South Korean Hwan (1953–1962)",
			            "displayName-count-one": "South Korean hwan (1953–1962)",
			            "displayName-count-other": "South Korean hwan (1953–1962)",
			            "symbol": "KRH"
			          },
			          "KRO": {
			            "displayName": "South Korean Won (1945–1953)",
			            "displayName-count-one": "South Korean won (1945–1953)",
			            "displayName-count-other": "South Korean won (1945–1953)",
			            "symbol": "KRO"
			          },
			          "KRW": {
			            "displayName": "South Korean Won",
			            "displayName-count-one": "South Korean won",
			            "displayName-count-other": "South Korean won",
			            "symbol": "₩",
			            "symbol-alt-narrow": "₩"
			          },
			          "KWD": {
			            "displayName": "Kuwaiti Dinar",
			            "displayName-count-one": "Kuwaiti dinar",
			            "displayName-count-other": "Kuwaiti dinars",
			            "symbol": "KWD"
			          },
			          "KYD": {
			            "displayName": "Cayman Islands Dollar",
			            "displayName-count-one": "Cayman Islands dollar",
			            "displayName-count-other": "Cayman Islands dollars",
			            "symbol": "KYD",
			            "symbol-alt-narrow": "$"
			          },
			          "KZT": {
			            "displayName": "Kazakhstani Tenge",
			            "displayName-count-one": "Kazakhstani tenge",
			            "displayName-count-other": "Kazakhstani tenges",
			            "symbol": "KZT",
			            "symbol-alt-narrow": "₸"
			          },
			          "LAK": {
			            "displayName": "Laotian Kip",
			            "displayName-count-one": "Laotian kip",
			            "displayName-count-other": "Laotian kips",
			            "symbol": "LAK",
			            "symbol-alt-narrow": "₭"
			          },
			          "LBP": {
			            "displayName": "Lebanese Pound",
			            "displayName-count-one": "Lebanese pound",
			            "displayName-count-other": "Lebanese pounds",
			            "symbol": "LBP",
			            "symbol-alt-narrow": "L£"
			          },
			          "LKR": {
			            "displayName": "Sri Lankan Rupee",
			            "displayName-count-one": "Sri Lankan rupee",
			            "displayName-count-other": "Sri Lankan rupees",
			            "symbol": "LKR",
			            "symbol-alt-narrow": "Rs"
			          },
			          "LRD": {
			            "displayName": "Liberian Dollar",
			            "displayName-count-one": "Liberian dollar",
			            "displayName-count-other": "Liberian dollars",
			            "symbol": "LRD",
			            "symbol-alt-narrow": "$"
			          },
			          "LSL": {
			            "displayName": "Lesotho Loti",
			            "displayName-count-one": "Lesotho loti",
			            "displayName-count-other": "Lesotho lotis",
			            "symbol": "LSL"
			          },
			          "LTL": {
			            "displayName": "Lithuanian Litas",
			            "displayName-count-one": "Lithuanian litas",
			            "displayName-count-other": "Lithuanian litai",
			            "symbol": "LTL",
			            "symbol-alt-narrow": "Lt"
			          },
			          "LTT": {
			            "displayName": "Lithuanian Talonas",
			            "displayName-count-one": "Lithuanian talonas",
			            "displayName-count-other": "Lithuanian talonases",
			            "symbol": "LTT"
			          },
			          "LUC": {
			            "displayName": "Luxembourgian Convertible Franc",
			            "displayName-count-one": "Luxembourgian convertible franc",
			            "displayName-count-other": "Luxembourgian convertible francs",
			            "symbol": "LUC"
			          },
			          "LUF": {
			            "displayName": "Luxembourgian Franc",
			            "displayName-count-one": "Luxembourgian franc",
			            "displayName-count-other": "Luxembourgian francs",
			            "symbol": "LUF"
			          },
			          "LUL": {
			            "displayName": "Luxembourg Financial Franc",
			            "displayName-count-one": "Luxembourg financial franc",
			            "displayName-count-other": "Luxembourg financial francs",
			            "symbol": "LUL"
			          },
			          "LVL": {
			            "displayName": "Latvian Lats",
			            "displayName-count-one": "Latvian lats",
			            "displayName-count-other": "Latvian lati",
			            "symbol": "LVL",
			            "symbol-alt-narrow": "Ls"
			          },
			          "LVR": {
			            "displayName": "Latvian Ruble",
			            "displayName-count-one": "Latvian ruble",
			            "displayName-count-other": "Latvian rubles",
			            "symbol": "LVR"
			          },
			          "LYD": {
			            "displayName": "Libyan Dinar",
			            "displayName-count-one": "Libyan dinar",
			            "displayName-count-other": "Libyan dinars",
			            "symbol": "LYD"
			          },
			          "MAD": {
			            "displayName": "Moroccan Dirham",
			            "displayName-count-one": "Moroccan dirham",
			            "displayName-count-other": "Moroccan dirhams",
			            "symbol": "MAD"
			          },
			          "MAF": {
			            "displayName": "Moroccan Franc",
			            "displayName-count-one": "Moroccan franc",
			            "displayName-count-other": "Moroccan francs",
			            "symbol": "MAF"
			          },
			          "MCF": {
			            "displayName": "Monegasque Franc",
			            "displayName-count-one": "Monegasque franc",
			            "displayName-count-other": "Monegasque francs",
			            "symbol": "MCF"
			          },
			          "MDC": {
			            "displayName": "Moldovan Cupon",
			            "displayName-count-one": "Moldovan cupon",
			            "displayName-count-other": "Moldovan cupon",
			            "symbol": "MDC"
			          },
			          "MDL": {
			            "displayName": "Moldovan Leu",
			            "displayName-count-one": "Moldovan leu",
			            "displayName-count-other": "Moldovan lei",
			            "symbol": "MDL"
			          },
			          "MGA": {
			            "displayName": "Malagasy Ariary",
			            "displayName-count-one": "Malagasy Ariary",
			            "displayName-count-other": "Malagasy Ariaries",
			            "symbol": "MGA",
			            "symbol-alt-narrow": "Ar"
			          },
			          "MGF": {
			            "displayName": "Malagasy Franc",
			            "displayName-count-one": "Malagasy franc",
			            "displayName-count-other": "Malagasy francs",
			            "symbol": "MGF"
			          },
			          "MKD": {
			            "displayName": "Macedonian Denar",
			            "displayName-count-one": "Macedonian denar",
			            "displayName-count-other": "Macedonian denari",
			            "symbol": "MKD"
			          },
			          "MKN": {
			            "displayName": "Macedonian Denar (1992–1993)",
			            "displayName-count-one": "Macedonian denar (1992–1993)",
			            "displayName-count-other": "Macedonian denari (1992–1993)",
			            "symbol": "MKN"
			          },
			          "MLF": {
			            "displayName": "Malian Franc",
			            "displayName-count-one": "Malian franc",
			            "displayName-count-other": "Malian francs",
			            "symbol": "MLF"
			          },
			          "MMK": {
			            "displayName": "Myanmar Kyat",
			            "displayName-count-one": "Myanmar kyat",
			            "displayName-count-other": "Myanmar kyats",
			            "symbol": "MMK",
			            "symbol-alt-narrow": "K"
			          },
			          "MNT": {
			            "displayName": "Mongolian Tugrik",
			            "displayName-count-one": "Mongolian tugrik",
			            "displayName-count-other": "Mongolian tugriks",
			            "symbol": "MNT",
			            "symbol-alt-narrow": "₮"
			          },
			          "MOP": {
			            "displayName": "Macanese Pataca",
			            "displayName-count-one": "Macanese pataca",
			            "displayName-count-other": "Macanese patacas",
			            "symbol": "MOP"
			          },
			          "MRO": {
			            "displayName": "Mauritanian Ouguiya",
			            "displayName-count-one": "Mauritanian ouguiya",
			            "displayName-count-other": "Mauritanian ouguiyas",
			            "symbol": "MRO"
			          },
			          "MTL": {
			            "displayName": "Maltese Lira",
			            "displayName-count-one": "Maltese lira",
			            "displayName-count-other": "Maltese lira",
			            "symbol": "MTL"
			          },
			          "MTP": {
			            "displayName": "Maltese Pound",
			            "displayName-count-one": "Maltese pound",
			            "displayName-count-other": "Maltese pounds",
			            "symbol": "MTP"
			          },
			          "MUR": {
			            "displayName": "Mauritian Rupee",
			            "displayName-count-one": "Mauritian rupee",
			            "displayName-count-other": "Mauritian rupees",
			            "symbol": "MUR",
			            "symbol-alt-narrow": "Rs"
			          },
			          "MVP": {
			            "displayName": "Maldivian Rupee (1947–1981)",
			            "displayName-count-one": "Maldivian rupee (1947–1981)",
			            "displayName-count-other": "Maldivian rupees (1947–1981)"
			          },
			          "MVR": {
			            "displayName": "Maldivian Rufiyaa",
			            "displayName-count-one": "Maldivian rufiyaa",
			            "displayName-count-other": "Maldivian rufiyaas",
			            "symbol": "MVR"
			          },
			          "MWK": {
			            "displayName": "Malawian Kwacha",
			            "displayName-count-one": "Malawian Kwacha",
			            "displayName-count-other": "Malawian Kwachas",
			            "symbol": "MWK"
			          },
			          "MXN": {
			            "displayName": "Mexican Peso",
			            "displayName-count-one": "Mexican peso",
			            "displayName-count-other": "Mexican pesos",
			            "symbol": "MX$",
			            "symbol-alt-narrow": "$"
			          },
			          "MXP": {
			            "displayName": "Mexican Silver Peso (1861–1992)",
			            "displayName-count-one": "Mexican silver peso (1861–1992)",
			            "displayName-count-other": "Mexican silver pesos (1861–1992)",
			            "symbol": "MXP"
			          },
			          "MXV": {
			            "displayName": "Mexican Investment Unit",
			            "displayName-count-one": "Mexican investment unit",
			            "displayName-count-other": "Mexican investment units",
			            "symbol": "MXV"
			          },
			          "MYR": {
			            "displayName": "Malaysian Ringgit",
			            "displayName-count-one": "Malaysian ringgit",
			            "displayName-count-other": "Malaysian ringgits",
			            "symbol": "MYR",
			            "symbol-alt-narrow": "RM"
			          },
			          "MZE": {
			            "displayName": "Mozambican Escudo",
			            "displayName-count-one": "Mozambican escudo",
			            "displayName-count-other": "Mozambican escudos",
			            "symbol": "MZE"
			          },
			          "MZM": {
			            "displayName": "Mozambican Metical (1980–2006)",
			            "displayName-count-one": "Mozambican metical (1980–2006)",
			            "displayName-count-other": "Mozambican meticals (1980–2006)",
			            "symbol": "MZM"
			          },
			          "MZN": {
			            "displayName": "Mozambican Metical",
			            "displayName-count-one": "Mozambican metical",
			            "displayName-count-other": "Mozambican meticals",
			            "symbol": "MZN"
			          },
			          "NAD": {
			            "displayName": "Namibian Dollar",
			            "displayName-count-one": "Namibian dollar",
			            "displayName-count-other": "Namibian dollars",
			            "symbol": "NAD",
			            "symbol-alt-narrow": "$"
			          },
			          "NGN": {
			            "displayName": "Nigerian Naira",
			            "displayName-count-one": "Nigerian naira",
			            "displayName-count-other": "Nigerian nairas",
			            "symbol": "NGN",
			            "symbol-alt-narrow": "₦"
			          },
			          "NIC": {
			            "displayName": "Nicaraguan Córdoba (1988–1991)",
			            "displayName-count-one": "Nicaraguan córdoba (1988–1991)",
			            "displayName-count-other": "Nicaraguan córdobas (1988–1991)",
			            "symbol": "NIC"
			          },
			          "NIO": {
			            "displayName": "Nicaraguan Córdoba",
			            "displayName-count-one": "Nicaraguan córdoba",
			            "displayName-count-other": "Nicaraguan córdobas",
			            "symbol": "NIO",
			            "symbol-alt-narrow": "C$"
			          },
			          "NLG": {
			            "displayName": "Dutch Guilder",
			            "displayName-count-one": "Dutch guilder",
			            "displayName-count-other": "Dutch guilders",
			            "symbol": "NLG"
			          },
			          "NOK": {
			            "displayName": "Norwegian Krone",
			            "displayName-count-one": "Norwegian krone",
			            "displayName-count-other": "Norwegian kroner",
			            "symbol": "NOK",
			            "symbol-alt-narrow": "kr"
			          },
			          "NPR": {
			            "displayName": "Nepalese Rupee",
			            "displayName-count-one": "Nepalese rupee",
			            "displayName-count-other": "Nepalese rupees",
			            "symbol": "NPR",
			            "symbol-alt-narrow": "Rs"
			          },
			          "NZD": {
			            "displayName": "New Zealand Dollar",
			            "displayName-count-one": "New Zealand dollar",
			            "displayName-count-other": "New Zealand dollars",
			            "symbol": "NZ$",
			            "symbol-alt-narrow": "$"
			          },
			          "OMR": {
			            "displayName": "Omani Rial",
			            "displayName-count-one": "Omani rial",
			            "displayName-count-other": "Omani rials",
			            "symbol": "OMR"
			          },
			          "PAB": {
			            "displayName": "Panamanian Balboa",
			            "displayName-count-one": "Panamanian balboa",
			            "displayName-count-other": "Panamanian balboas",
			            "symbol": "PAB"
			          },
			          "PEI": {
			            "displayName": "Peruvian Inti",
			            "displayName-count-one": "Peruvian inti",
			            "displayName-count-other": "Peruvian intis",
			            "symbol": "PEI"
			          },
			          "PEN": {
			            "displayName": "Peruvian Nuevo Sol",
			            "displayName-count-one": "Peruvian nuevo sol",
			            "displayName-count-other": "Peruvian nuevos soles",
			            "symbol": "PEN"
			          },
			          "PES": {
			            "displayName": "Peruvian Sol (1863–1965)",
			            "displayName-count-one": "Peruvian sol (1863–1965)",
			            "displayName-count-other": "Peruvian soles (1863–1965)",
			            "symbol": "PES"
			          },
			          "PGK": {
			            "displayName": "Papua New Guinean Kina",
			            "displayName-count-one": "Papua New Guinean kina",
			            "displayName-count-other": "Papua New Guinean kina",
			            "symbol": "PGK"
			          },
			          "PHP": {
			            "displayName": "Philippine Peso",
			            "displayName-count-one": "Philippine peso",
			            "displayName-count-other": "Philippine pesos",
			            "symbol": "PHP",
			            "symbol-alt-narrow": "₱"
			          },
			          "PKR": {
			            "displayName": "Pakistani Rupee",
			            "displayName-count-one": "Pakistani rupee",
			            "displayName-count-other": "Pakistani rupees",
			            "symbol": "PKR",
			            "symbol-alt-narrow": "Rs"
			          },
			          "PLN": {
			            "displayName": "Polish Zloty",
			            "displayName-count-one": "Polish zloty",
			            "displayName-count-other": "Polish zlotys",
			            "symbol": "PLN",
			            "symbol-alt-narrow": "zł"
			          },
			          "PLZ": {
			            "displayName": "Polish Zloty (1950–1995)",
			            "displayName-count-one": "Polish zloty (PLZ)",
			            "displayName-count-other": "Polish zlotys (PLZ)",
			            "symbol": "PLZ"
			          },
			          "PTE": {
			            "displayName": "Portuguese Escudo",
			            "displayName-count-one": "Portuguese escudo",
			            "displayName-count-other": "Portuguese escudos",
			            "symbol": "PTE"
			          },
			          "PYG": {
			            "displayName": "Paraguayan Guarani",
			            "displayName-count-one": "Paraguayan guarani",
			            "displayName-count-other": "Paraguayan guaranis",
			            "symbol": "PYG",
			            "symbol-alt-narrow": "₲"
			          },
			          "QAR": {
			            "displayName": "Qatari Rial",
			            "displayName-count-one": "Qatari rial",
			            "displayName-count-other": "Qatari rials",
			            "symbol": "QAR"
			          },
			          "RHD": {
			            "displayName": "Rhodesian Dollar",
			            "displayName-count-one": "Rhodesian dollar",
			            "displayName-count-other": "Rhodesian dollars",
			            "symbol": "RHD"
			          },
			          "ROL": {
			            "displayName": "Romanian Leu (1952–2006)",
			            "displayName-count-one": "Romanian leu (1952–2006)",
			            "displayName-count-other": "Romanian Lei (1952–2006)",
			            "symbol": "ROL"
			          },
			          "RON": {
			            "displayName": "Romanian Leu",
			            "displayName-count-one": "Romanian leu",
			            "displayName-count-other": "Romanian lei",
			            "symbol": "RON"
			          },
			          "RSD": {
			            "displayName": "Serbian Dinar",
			            "displayName-count-one": "Serbian dinar",
			            "displayName-count-other": "Serbian dinars",
			            "symbol": "RSD"
			          },
			          "RUB": {
			            "displayName": "Russian Ruble",
			            "displayName-count-one": "Russian ruble",
			            "displayName-count-other": "Russian rubles",
			            "symbol": "RUB",
			            "symbol-alt-variant": "₽"
			          },
			          "RUR": {
			            "displayName": "Russian Ruble (1991–1998)",
			            "displayName-count-one": "Russian ruble (1991–1998)",
			            "displayName-count-other": "Russian rubles (1991–1998)",
			            "symbol": "RUR",
			            "symbol-alt-narrow": "р."
			          },
			          "RWF": {
			            "displayName": "Rwandan Franc",
			            "displayName-count-one": "Rwandan franc",
			            "displayName-count-other": "Rwandan francs",
			            "symbol": "RWF",
			            "symbol-alt-narrow": "RF"
			          },
			          "SAR": {
			            "displayName": "Saudi Riyal",
			            "displayName-count-one": "Saudi riyal",
			            "displayName-count-other": "Saudi riyals",
			            "symbol": "SAR"
			          },
			          "SBD": {
			            "displayName": "Solomon Islands Dollar",
			            "displayName-count-one": "Solomon Islands dollar",
			            "displayName-count-other": "Solomon Islands dollars",
			            "symbol": "SBD",
			            "symbol-alt-narrow": "$"
			          },
			          "SCR": {
			            "displayName": "Seychellois Rupee",
			            "displayName-count-one": "Seychellois rupee",
			            "displayName-count-other": "Seychellois rupees",
			            "symbol": "SCR"
			          },
			          "SDD": {
			            "displayName": "Sudanese Dinar (1992–2007)",
			            "displayName-count-one": "Sudanese dinar (1992–2007)",
			            "displayName-count-other": "Sudanese dinars (1992–2007)",
			            "symbol": "SDD"
			          },
			          "SDG": {
			            "displayName": "Sudanese Pound",
			            "displayName-count-one": "Sudanese pound",
			            "displayName-count-other": "Sudanese pounds",
			            "symbol": "SDG"
			          },
			          "SDP": {
			            "displayName": "Sudanese Pound (1957–1998)",
			            "displayName-count-one": "Sudanese pound (1957–1998)",
			            "displayName-count-other": "Sudanese pounds (1957–1998)",
			            "symbol": "SDP"
			          },
			          "SEK": {
			            "displayName": "Swedish Krona",
			            "displayName-count-one": "Swedish krona",
			            "displayName-count-other": "Swedish kronor",
			            "symbol": "SEK",
			            "symbol-alt-narrow": "kr"
			          },
			          "SGD": {
			            "displayName": "Singapore Dollar",
			            "displayName-count-one": "Singapore dollar",
			            "displayName-count-other": "Singapore dollars",
			            "symbol": "SGD",
			            "symbol-alt-narrow": "$"
			          },
			          "SHP": {
			            "displayName": "St. Helena Pound",
			            "displayName-count-one": "St. Helena pound",
			            "displayName-count-other": "St. Helena pounds",
			            "symbol": "SHP",
			            "symbol-alt-narrow": "£"
			          },
			          "SIT": {
			            "displayName": "Slovenian Tolar",
			            "displayName-count-one": "Slovenian tolar",
			            "displayName-count-other": "Slovenian tolars",
			            "symbol": "SIT"
			          },
			          "SKK": {
			            "displayName": "Slovak Koruna",
			            "displayName-count-one": "Slovak koruna",
			            "displayName-count-other": "Slovak korunas",
			            "symbol": "SKK"
			          },
			          "SLL": {
			            "displayName": "Sierra Leonean Leone",
			            "displayName-count-one": "Sierra Leonean leone",
			            "displayName-count-other": "Sierra Leonean leones",
			            "symbol": "SLL"
			          },
			          "SOS": {
			            "displayName": "Somali Shilling",
			            "displayName-count-one": "Somali shilling",
			            "displayName-count-other": "Somali shillings",
			            "symbol": "SOS"
			          },
			          "SRD": {
			            "displayName": "Surinamese Dollar",
			            "displayName-count-one": "Surinamese dollar",
			            "displayName-count-other": "Surinamese dollars",
			            "symbol": "SRD",
			            "symbol-alt-narrow": "$"
			          },
			          "SRG": {
			            "displayName": "Surinamese Guilder",
			            "displayName-count-one": "Surinamese guilder",
			            "displayName-count-other": "Surinamese guilders",
			            "symbol": "SRG"
			          },
			          "SSP": {
			            "displayName": "South Sudanese Pound",
			            "displayName-count-one": "South Sudanese pound",
			            "displayName-count-other": "South Sudanese pounds",
			            "symbol": "SSP",
			            "symbol-alt-narrow": "£"
			          },
			          "STD": {
			            "displayName": "São Tomé & Príncipe Dobra",
			            "displayName-count-one": "São Tomé & Príncipe dobra",
			            "displayName-count-other": "São Tomé & Príncipe dobras",
			            "symbol": "STD",
			            "symbol-alt-narrow": "Db"
			          },
			          "SUR": {
			            "displayName": "Soviet Rouble",
			            "displayName-count-one": "Soviet rouble",
			            "displayName-count-other": "Soviet roubles",
			            "symbol": "SUR"
			          },
			          "SVC": {
			            "displayName": "Salvadoran Colón",
			            "displayName-count-one": "Salvadoran colón",
			            "displayName-count-other": "Salvadoran colones",
			            "symbol": "SVC"
			          },
			          "SYP": {
			            "displayName": "Syrian Pound",
			            "displayName-count-one": "Syrian pound",
			            "displayName-count-other": "Syrian pounds",
			            "symbol": "SYP",
			            "symbol-alt-narrow": "£"
			          },
			          "SZL": {
			            "displayName": "Swazi Lilangeni",
			            "displayName-count-one": "Swazi lilangeni",
			            "displayName-count-other": "Swazi emalangeni",
			            "symbol": "SZL"
			          },
			          "THB": {
			            "displayName": "Thai Baht",
			            "displayName-count-one": "Thai baht",
			            "displayName-count-other": "Thai baht",
			            "symbol": "THB",
			            "symbol-alt-narrow": "฿"
			          },
			          "TJR": {
			            "displayName": "Tajikistani Ruble",
			            "displayName-count-one": "Tajikistani ruble",
			            "displayName-count-other": "Tajikistani rubles",
			            "symbol": "TJR"
			          },
			          "TJS": {
			            "displayName": "Tajikistani Somoni",
			            "displayName-count-one": "Tajikistani somoni",
			            "displayName-count-other": "Tajikistani somonis",
			            "symbol": "TJS"
			          },
			          "TMM": {
			            "displayName": "Turkmenistani Manat (1993–2009)",
			            "displayName-count-one": "Turkmenistani manat (1993–2009)",
			            "displayName-count-other": "Turkmenistani manat (1993–2009)",
			            "symbol": "TMM"
			          },
			          "TMT": {
			            "displayName": "Turkmenistani Manat",
			            "displayName-count-one": "Turkmenistani manat",
			            "displayName-count-other": "Turkmenistani manat",
			            "symbol": "TMT"
			          },
			          "TND": {
			            "displayName": "Tunisian Dinar",
			            "displayName-count-one": "Tunisian dinar",
			            "displayName-count-other": "Tunisian dinars",
			            "symbol": "TND"
			          },
			          "TOP": {
			            "displayName": "Tongan Paʻanga",
			            "displayName-count-one": "Tongan paʻanga",
			            "displayName-count-other": "Tongan paʻanga",
			            "symbol": "TOP",
			            "symbol-alt-narrow": "T$"
			          },
			          "TPE": {
			            "displayName": "Timorese Escudo",
			            "displayName-count-one": "Timorese escudo",
			            "displayName-count-other": "Timorese escudos",
			            "symbol": "TPE"
			          },
			          "TRL": {
			            "displayName": "Turkish Lira (1922–2005)",
			            "displayName-count-one": "Turkish lira (1922–2005)",
			            "displayName-count-other": "Turkish Lira (1922–2005)",
			            "symbol": "TRL"
			          },
			          "TRY": {
			            "displayName": "Turkish Lira",
			            "displayName-count-one": "Turkish lira",
			            "displayName-count-other": "Turkish Lira",
			            "symbol": "TRY",
			            "symbol-alt-narrow": "₺",
			            "symbol-alt-variant": "TL"
			          },
			          "TTD": {
			            "displayName": "Trinidad & Tobago Dollar",
			            "displayName-count-one": "Trinidad & Tobago dollar",
			            "displayName-count-other": "Trinidad & Tobago dollars",
			            "symbol": "TTD",
			            "symbol-alt-narrow": "$"
			          },
			          "TWD": {
			            "displayName": "New Taiwan Dollar",
			            "displayName-count-one": "New Taiwan dollar",
			            "displayName-count-other": "New Taiwan dollars",
			            "symbol": "NT$",
			            "symbol-alt-narrow": "NT$"
			          },
			          "TZS": {
			            "displayName": "Tanzanian Shilling",
			            "displayName-count-one": "Tanzanian shilling",
			            "displayName-count-other": "Tanzanian shillings",
			            "symbol": "TZS"
			          },
			          "UAH": {
			            "displayName": "Ukrainian Hryvnia",
			            "displayName-count-one": "Ukrainian hryvnia",
			            "displayName-count-other": "Ukrainian hryvnias",
			            "symbol": "UAH",
			            "symbol-alt-narrow": "₴"
			          },
			          "UAK": {
			            "displayName": "Ukrainian Karbovanets",
			            "displayName-count-one": "Ukrainian karbovanets",
			            "displayName-count-other": "Ukrainian karbovantsiv",
			            "symbol": "UAK"
			          },
			          "UGS": {
			            "displayName": "Ugandan Shilling (1966–1987)",
			            "displayName-count-one": "Ugandan shilling (1966–1987)",
			            "displayName-count-other": "Ugandan shillings (1966–1987)",
			            "symbol": "UGS"
			          },
			          "UGX": {
			            "displayName": "Ugandan Shilling",
			            "displayName-count-one": "Ugandan shilling",
			            "displayName-count-other": "Ugandan shillings",
			            "symbol": "UGX"
			          },
			          "USD": {
			            "displayName": "US Dollar",
			            "displayName-count-one": "US dollar",
			            "displayName-count-other": "US dollars",
			            "symbol": "$",
			            "symbol-alt-narrow": "$"
			          },
			          "USN": {
			            "displayName": "US Dollar (Next day)",
			            "displayName-count-one": "US dollar (next day)",
			            "displayName-count-other": "US dollars (next day)",
			            "symbol": "USN"
			          },
			          "USS": {
			            "displayName": "US Dollar (Same day)",
			            "displayName-count-one": "US dollar (same day)",
			            "displayName-count-other": "US dollars (same day)",
			            "symbol": "USS"
			          },
			          "UYI": {
			            "displayName": "Uruguayan Peso (Indexed Units)",
			            "displayName-count-one": "Uruguayan peso (indexed units)",
			            "displayName-count-other": "Uruguayan pesos (indexed units)",
			            "symbol": "UYI"
			          },
			          "UYP": {
			            "displayName": "Uruguayan Peso (1975–1993)",
			            "displayName-count-one": "Uruguayan peso (1975–1993)",
			            "displayName-count-other": "Uruguayan pesos (1975–1993)",
			            "symbol": "UYP"
			          },
			          "UYU": {
			            "displayName": "Uruguayan Peso",
			            "displayName-count-one": "Uruguayan peso",
			            "displayName-count-other": "Uruguayan pesos",
			            "symbol": "UYU",
			            "symbol-alt-narrow": "$"
			          },
			          "UZS": {
			            "displayName": "Uzbekistan Som",
			            "displayName-count-one": "Uzbekistan som",
			            "displayName-count-other": "Uzbekistan som",
			            "symbol": "UZS"
			          },
			          "VEB": {
			            "displayName": "Venezuelan Bolívar (1871–2008)",
			            "displayName-count-one": "Venezuelan bolívar (1871–2008)",
			            "displayName-count-other": "Venezuelan bolívars (1871–2008)",
			            "symbol": "VEB"
			          },
			          "VEF": {
			            "displayName": "Venezuelan Bolívar",
			            "displayName-count-one": "Venezuelan bolívar",
			            "displayName-count-other": "Venezuelan bolívars",
			            "symbol": "VEF",
			            "symbol-alt-narrow": "Bs"
			          },
			          "VND": {
			            "displayName": "Vietnamese Dong",
			            "displayName-count-one": "Vietnamese dong",
			            "displayName-count-other": "Vietnamese dong",
			            "symbol": "₫",
			            "symbol-alt-narrow": "₫"
			          },
			          "VNN": {
			            "displayName": "Vietnamese Dong (1978–1985)",
			            "displayName-count-one": "Vietnamese dong (1978–1985)",
			            "displayName-count-other": "Vietnamese dong (1978–1985)",
			            "symbol": "VNN"
			          },
			          "VUV": {
			            "displayName": "Vanuatu Vatu",
			            "displayName-count-one": "Vanuatu vatu",
			            "displayName-count-other": "Vanuatu vatus",
			            "symbol": "VUV"
			          },
			          "WST": {
			            "displayName": "Samoan Tala",
			            "displayName-count-one": "Samoan tala",
			            "displayName-count-other": "Samoan tala",
			            "symbol": "WST"
			          },
			          "XAF": {
			            "displayName": "CFA Franc BEAC",
			            "displayName-count-one": "CFA franc BEAC",
			            "displayName-count-other": "CFA francs BEAC",
			            "symbol": "FCFA"
			          },
			          "XAG": {
			            "displayName": "Silver",
			            "displayName-count-one": "troy ounce of silver",
			            "displayName-count-other": "troy ounces of silver",
			            "symbol": "XAG"
			          },
			          "XAU": {
			            "displayName": "Gold",
			            "displayName-count-one": "troy ounce of gold",
			            "displayName-count-other": "troy ounces of gold",
			            "symbol": "XAU"
			          },
			          "XBA": {
			            "displayName": "European Composite Unit",
			            "displayName-count-one": "European composite unit",
			            "displayName-count-other": "European composite units",
			            "symbol": "XBA"
			          },
			          "XBB": {
			            "displayName": "European Monetary Unit",
			            "displayName-count-one": "European monetary unit",
			            "displayName-count-other": "European monetary units",
			            "symbol": "XBB"
			          },
			          "XBC": {
			            "displayName": "European Unit of Account (XBC)",
			            "displayName-count-one": "European unit of account (XBC)",
			            "displayName-count-other": "European units of account (XBC)",
			            "symbol": "XBC"
			          },
			          "XBD": {
			            "displayName": "European Unit of Account (XBD)",
			            "displayName-count-one": "European unit of account (XBD)",
			            "displayName-count-other": "European units of account (XBD)",
			            "symbol": "XBD"
			          },
			          "XCD": {
			            "displayName": "East Caribbean Dollar",
			            "displayName-count-one": "East Caribbean dollar",
			            "displayName-count-other": "East Caribbean dollars",
			            "symbol": "EC$",
			            "symbol-alt-narrow": "$"
			          },
			          "XDR": {
			            "displayName": "Special Drawing Rights",
			            "displayName-count-one": "special drawing rights",
			            "displayName-count-other": "special drawing rights",
			            "symbol": "XDR"
			          },
			          "XEU": {
			            "displayName": "European Currency Unit",
			            "displayName-count-one": "European currency unit",
			            "displayName-count-other": "European currency units",
			            "symbol": "XEU"
			          },
			          "XFO": {
			            "displayName": "French Gold Franc",
			            "displayName-count-one": "French gold franc",
			            "displayName-count-other": "French gold francs",
			            "symbol": "XFO"
			          },
			          "XFU": {
			            "displayName": "French UIC-Franc",
			            "displayName-count-one": "French UIC-franc",
			            "displayName-count-other": "French UIC-francs",
			            "symbol": "XFU"
			          },
			          "XOF": {
			            "displayName": "CFA Franc BCEAO",
			            "displayName-count-one": "CFA franc BCEAO",
			            "displayName-count-other": "CFA francs BCEAO",
			            "symbol": "CFA"
			          },
			          "XPD": {
			            "displayName": "Palladium",
			            "displayName-count-one": "troy ounce of palladium",
			            "displayName-count-other": "troy ounces of palladium",
			            "symbol": "XPD"
			          },
			          "XPF": {
			            "displayName": "CFP Franc",
			            "displayName-count-one": "CFP franc",
			            "displayName-count-other": "CFP francs",
			            "symbol": "CFPF"
			          },
			          "XPT": {
			            "displayName": "Platinum",
			            "displayName-count-one": "troy ounce of platinum",
			            "displayName-count-other": "troy ounces of platinum",
			            "symbol": "XPT"
			          },
			          "XRE": {
			            "displayName": "RINET Funds",
			            "displayName-count-one": "RINET Funds unit",
			            "displayName-count-other": "RINET Funds units",
			            "symbol": "XRE"
			          },
			          "XSU": {
			            "displayName": "Sucre",
			            "displayName-count-one": "Sucre",
			            "displayName-count-other": "Sucres",
			            "symbol": "XSU"
			          },
			          "XTS": {
			            "displayName": "Testing Currency Code",
			            "displayName-count-one": "Testing Currency unit",
			            "displayName-count-other": "Testing Currency units",
			            "symbol": "XTS"
			          },
			          "XUA": {
			            "displayName": "ADB Unit of Account",
			            "displayName-count-one": "ADB unit of account",
			            "displayName-count-other": "ADB units of account",
			            "symbol": "XUA"
			          },
			          "XXX": {
			            "displayName": "Unknown Currency",
			            "displayName-count-one": "(unknown unit of currency)",
			            "displayName-count-other": "(unknown currency)",
			            "symbol": "XXX"
			          },
			          "YDD": {
			            "displayName": "Yemeni Dinar",
			            "displayName-count-one": "Yemeni dinar",
			            "displayName-count-other": "Yemeni dinars",
			            "symbol": "YDD"
			          },
			          "YER": {
			            "displayName": "Yemeni Rial",
			            "displayName-count-one": "Yemeni rial",
			            "displayName-count-other": "Yemeni rials",
			            "symbol": "YER"
			          },
			          "YUD": {
			            "displayName": "Yugoslavian Hard Dinar (1966–1990)",
			            "displayName-count-one": "Yugoslavian hard dinar (1966–1990)",
			            "displayName-count-other": "Yugoslavian hard dinars (1966–1990)",
			            "symbol": "YUD"
			          },
			          "YUM": {
			            "displayName": "Yugoslavian New Dinar (1994–2002)",
			            "displayName-count-one": "Yugoslavian new dinar (1994–2002)",
			            "displayName-count-other": "Yugoslavian new dinars (1994–2002)",
			            "symbol": "YUM"
			          },
			          "YUN": {
			            "displayName": "Yugoslavian Convertible Dinar (1990–1992)",
			            "displayName-count-one": "Yugoslavian convertible dinar (1990–1992)",
			            "displayName-count-other": "Yugoslavian convertible dinars (1990–1992)",
			            "symbol": "YUN"
			          },
			          "YUR": {
			            "displayName": "Yugoslavian Reformed Dinar (1992–1993)",
			            "displayName-count-one": "Yugoslavian reformed dinar (1992–1993)",
			            "displayName-count-other": "Yugoslavian reformed dinars (1992–1993)",
			            "symbol": "YUR"
			          },
			          "ZAL": {
			            "displayName": "South African Rand (financial)",
			            "displayName-count-one": "South African rand (financial)",
			            "displayName-count-other": "South African rands (financial)",
			            "symbol": "ZAL"
			          },
			          "ZAR": {
			            "displayName": "South African Rand",
			            "displayName-count-one": "South African rand",
			            "displayName-count-other": "South African rand",
			            "symbol": "ZAR",
			            "symbol-alt-narrow": "R"
			          },
			          "ZMK": {
			            "displayName": "Zambian Kwacha (1968–2012)",
			            "displayName-count-one": "Zambian kwacha (1968–2012)",
			            "displayName-count-other": "Zambian kwachas (1968–2012)",
			            "symbol": "ZMK"
			          },
			          "ZMW": {
			            "displayName": "Zambian Kwacha",
			            "displayName-count-one": "Zambian kwacha",
			            "displayName-count-other": "Zambian kwachas",
			            "symbol": "ZMW",
			            "symbol-alt-narrow": "ZK"
			          },
			          "ZRN": {
			            "displayName": "Zairean New Zaire (1993–1998)",
			            "displayName-count-one": "Zairean new zaire (1993–1998)",
			            "displayName-count-other": "Zairean new zaires (1993–1998)",
			            "symbol": "ZRN"
			          },
			          "ZRZ": {
			            "displayName": "Zairean Zaire (1971–1993)",
			            "displayName-count-one": "Zairean zaire (1971–1993)",
			            "displayName-count-other": "Zairean zaires (1971–1993)",
			            "symbol": "ZRZ"
			          },
			          "ZWD": {
			            "displayName": "Zimbabwean Dollar (1980–2008)",
			            "displayName-count-one": "Zimbabwean dollar (1980–2008)",
			            "displayName-count-other": "Zimbabwean dollars (1980–2008)",
			            "symbol": "ZWD"
			          },
			          "ZWL": {
			            "displayName": "Zimbabwean Dollar (2009)",
			            "displayName-count-one": "Zimbabwean dollar (2009)",
			            "displayName-count-other": "Zimbabwean dollars (2009)",
			            "symbol": "ZWL"
			          },
			          "ZWR": {
			            "displayName": "Zimbabwean Dollar (2008)",
			            "displayName-count-one": "Zimbabwean dollar (2008)",
			            "displayName-count-other": "Zimbabwean dollars (2008)",
			            "symbol": "ZWR"
			          }
			        }
			      }
			    }
			  }
			};


var fakeSupplementalCurrencyDataCatalog = {
		  "supplemental": {
			    "version": {
			      "_cldrVersion": "26",
			      "_number": "$Revision: 10969 $"
			    },
			    "generation": {
			      "_date": "$Date: 2014-09-11 12:17:53 -0500 (Thu, 11 Sep 2014) $"
			    },
			    "currencyData": {
			      "fractions": {
			        "ADP": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "AFN": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "ALL": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "AMD": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "BHD": {
			          "_rounding": "0",
			          "_digits": "3"
			        },
			        "BIF": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "BYR": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "CAD": {
			          "_rounding": "0",
			          "_digits": "2",
			          "_cashRounding": "5"
			        },
			        "CHF": {
			          "_rounding": "0",
			          "_digits": "2",
			          "_cashRounding": "5"
			        },
			        "CLF": {
			          "_rounding": "0",
			          "_digits": "4"
			        },
			        "CLP": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "COP": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "CRC": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "CZK": {
			          "_rounding": "0",
			          "_digits": "2",
			          "_cashDigits": "0",
			          "_cashRounding": "0"
			        },
			        "DEFAULT": {
			          "_rounding": "0",
			          "_digits": "2"
			        },
			        "DJF": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "ESP": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "GNF": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "GYD": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "HUF": {
			          "_rounding": "0",
			          "_digits": "2",
			          "_cashDigits": "0",
			          "_cashRounding": "0"
			        },
			        "IDR": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "IQD": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "IRR": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "ISK": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "ITL": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "JOD": {
			          "_rounding": "0",
			          "_digits": "3"
			        },
			        "JPY": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "KMF": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "KPW": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "KRW": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "KWD": {
			          "_rounding": "0",
			          "_digits": "3"
			        },
			        "LAK": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "LBP": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "LUF": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "LYD": {
			          "_rounding": "0",
			          "_digits": "3"
			        },
			        "MGA": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "MGF": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "MMK": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "MNT": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "MRO": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "MUR": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "OMR": {
			          "_rounding": "0",
			          "_digits": "3"
			        },
			        "PKR": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "PYG": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "RSD": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "RWF": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "SLL": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "SOS": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "STD": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "SYP": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "TMM": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "TND": {
			          "_rounding": "0",
			          "_digits": "3"
			        },
			        "TRL": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "TWD": {
			          "_rounding": "0",
			          "_digits": "2",
			          "_cashDigits": "0",
			          "_cashRounding": "0"
			        },
			        "TZS": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "UGX": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "UYI": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "UZS": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "VND": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "VUV": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "XAF": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "XOF": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "XPF": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "YER": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "ZMK": {
			          "_rounding": "0",
			          "_digits": "0"
			        },
			        "ZWD": {
			          "_rounding": "0",
			          "_digits": "0"
			        }
			      },
			      "region": {
			        "150": [
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "AC": [
			          {
			            "SHP": {
			              "_from": "1976-01-01"
			            }
			          }
			        ],
			        "AD": [
			          {
			            "ESP": {
			              "_to": "2002-02-28",
			              "_from": "1873-01-01"
			            }
			          },
			          {
			            "ADP": {
			              "_to": "2001-12-31",
			              "_from": "1936-01-01"
			            }
			          },
			          {
			            "FRF": {
			              "_to": "2002-02-17",
			              "_from": "1960-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "AE": [
			          {
			            "AED": {
			              "_from": "1973-05-19"
			            }
			          }
			        ],
			        "AF": [
			          {
			            "AFA": {
			              "_to": "2002-12-31",
			              "_from": "1927-03-14"
			            }
			          },
			          {
			            "AFN": {
			              "_from": "2002-10-07"
			            }
			          }
			        ],
			        "AG": [
			          {
			            "XCD": {
			              "_from": "1965-10-06"
			            }
			          }
			        ],
			        "AI": [
			          {
			            "XCD": {
			              "_from": "1965-10-06"
			            }
			          }
			        ],
			        "AL": [
			          {
			            "ALK": {
			              "_to": "1965-08-16",
			              "_from": "1946-11-01"
			            }
			          },
			          {
			            "ALL": {
			              "_from": "1965-08-16"
			            }
			          }
			        ],
			        "AM": [
			          {
			            "SUR": {
			              "_to": "1991-12-25",
			              "_from": "1961-01-01"
			            }
			          },
			          {
			            "RUR": {
			              "_to": "1993-11-22",
			              "_from": "1991-12-25"
			            }
			          },
			          {
			            "AMD": {
			              "_from": "1993-11-22"
			            }
			          }
			        ],
			        "AO": [
			          {
			            "AOK": {
			              "_to": "1991-03-01",
			              "_from": "1977-01-08"
			            }
			          },
			          {
			            "AON": {
			              "_to": "2000-02-01",
			              "_from": "1990-09-25"
			            }
			          },
			          {
			            "AOR": {
			              "_to": "2000-02-01",
			              "_from": "1995-07-01"
			            }
			          },
			          {
			            "AOA": {
			              "_from": "1999-12-13"
			            }
			          }
			        ],
			        "AQ": [
			          {
			            "XXX": {
			              "_tender": "false"
			            }
			          }
			        ],
			        "AR": [
			          {
			            "ARM": {
			              "_to": "1970-01-01",
			              "_from": "1881-11-05"
			            }
			          },
			          {
			            "ARL": {
			              "_to": "1983-06-01",
			              "_from": "1970-01-01"
			            }
			          },
			          {
			            "ARP": {
			              "_to": "1985-06-14",
			              "_from": "1983-06-01"
			            }
			          },
			          {
			            "ARA": {
			              "_to": "1992-01-01",
			              "_from": "1985-06-14"
			            }
			          },
			          {
			            "ARS": {
			              "_from": "1992-01-01"
			            }
			          }
			        ],
			        "AS": [
			          {
			            "USD": {
			              "_from": "1904-07-16"
			            }
			          }
			        ],
			        "AT": [
			          {
			            "ATS": {
			              "_to": "2002-02-28",
			              "_from": "1947-12-04"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "AU": [
			          {
			            "AUD": {
			              "_from": "1966-02-14"
			            }
			          }
			        ],
			        "AW": [
			          {
			            "ANG": {
			              "_to": "1986-01-01",
			              "_from": "1940-05-10"
			            }
			          },
			          {
			            "AWG": {
			              "_from": "1986-01-01"
			            }
			          }
			        ],
			        "AX": [
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "AZ": [
			          {
			            "SUR": {
			              "_to": "1991-12-25",
			              "_from": "1961-01-01"
			            }
			          },
			          {
			            "RUR": {
			              "_to": "1994-01-01",
			              "_from": "1991-12-25"
			            }
			          },
			          {
			            "AZM": {
			              "_to": "2006-12-31",
			              "_from": "1993-11-22"
			            }
			          },
			          {
			            "AZN": {
			              "_from": "2006-01-01"
			            }
			          }
			        ],
			        "BA": [
			          {
			            "YUD": {
			              "_to": "1990-01-01",
			              "_from": "1966-01-01"
			            }
			          },
			          {
			            "YUN": {
			              "_to": "1992-07-01",
			              "_from": "1990-01-01"
			            }
			          },
			          {
			            "YUR": {
			              "_to": "1993-10-01",
			              "_from": "1992-07-01"
			            }
			          },
			          {
			            "BAD": {
			              "_to": "1994-08-15",
			              "_from": "1992-07-01"
			            }
			          },
			          {
			            "BAN": {
			              "_to": "1997-07-01",
			              "_from": "1994-08-15"
			            }
			          },
			          {
			            "BAM": {
			              "_from": "1995-01-01"
			            }
			          }
			        ],
			        "BB": [
			          {
			            "XCD": {
			              "_to": "1973-12-03",
			              "_from": "1965-10-06"
			            }
			          },
			          {
			            "BBD": {
			              "_from": "1973-12-03"
			            }
			          }
			        ],
			        "BD": [
			          {
			            "INR": {
			              "_to": "1948-04-01",
			              "_from": "1835-08-17"
			            }
			          },
			          {
			            "PKR": {
			              "_to": "1972-01-01",
			              "_from": "1948-04-01"
			            }
			          },
			          {
			            "BDT": {
			              "_from": "1972-01-01"
			            }
			          }
			        ],
			        "BE": [
			          {
			            "NLG": {
			              "_to": "1831-02-07",
			              "_from": "1816-12-15"
			            }
			          },
			          {
			            "BEF": {
			              "_to": "2002-02-28",
			              "_from": "1831-02-07"
			            }
			          },
			          {
			            "BEC": {
			              "_to": "1990-03-05",
			              "_tender": "false",
			              "_from": "1970-01-01"
			            }
			          },
			          {
			            "BEL": {
			              "_to": "1990-03-05",
			              "_tender": "false",
			              "_from": "1970-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "BF": [
			          {
			            "XOF": {
			              "_from": "1984-08-04"
			            }
			          }
			        ],
			        "BG": [
			          {
			            "BGO": {
			              "_to": "1952-05-12",
			              "_from": "1879-07-08"
			            }
			          },
			          {
			            "BGM": {
			              "_to": "1962-01-01",
			              "_from": "1952-05-12"
			            }
			          },
			          {
			            "BGL": {
			              "_to": "1999-07-05",
			              "_from": "1962-01-01"
			            }
			          },
			          {
			            "BGN": {
			              "_from": "1999-07-05"
			            }
			          }
			        ],
			        "BH": [
			          {
			            "BHD": {
			              "_from": "1965-10-16"
			            }
			          }
			        ],
			        "BI": [
			          {
			            "BIF": {
			              "_from": "1964-05-19"
			            }
			          }
			        ],
			        "BJ": [
			          {
			            "XOF": {
			              "_from": "1975-11-30"
			            }
			          }
			        ],
			        "BL": [
			          {
			            "FRF": {
			              "_to": "2002-02-17",
			              "_from": "1960-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "BM": [
			          {
			            "BMD": {
			              "_from": "1970-02-06"
			            }
			          }
			        ],
			        "BN": [
			          {
			            "MYR": {
			              "_to": "1967-06-12",
			              "_from": "1963-09-16"
			            }
			          },
			          {
			            "BND": {
			              "_from": "1967-06-12"
			            }
			          }
			        ],
			        "BO": [
			          {
			            "BOV": {
			              "_tender": "false"
			            }
			          },
			          {
			            "BOL": {
			              "_to": "1963-01-01",
			              "_from": "1863-06-23"
			            }
			          },
			          {
			            "BOP": {
			              "_to": "1986-12-31",
			              "_from": "1963-01-01"
			            }
			          },
			          {
			            "BOB": {
			              "_from": "1987-01-01"
			            }
			          }
			        ],
			        "BQ": [
			          {
			            "ANG": {
			              "_to": "2011-01-01",
			              "_from": "2010-10-10"
			            }
			          },
			          {
			            "USD": {
			              "_from": "2011-01-01"
			            }
			          }
			        ],
			        "BR": [
			          {
			            "BRZ": {
			              "_to": "1967-02-13",
			              "_from": "1942-11-01"
			            }
			          },
			          {
			            "BRB": {
			              "_to": "1986-02-28",
			              "_from": "1967-02-13"
			            }
			          },
			          {
			            "BRC": {
			              "_to": "1989-01-15",
			              "_from": "1986-02-28"
			            }
			          },
			          {
			            "BRN": {
			              "_to": "1990-03-16",
			              "_from": "1989-01-15"
			            }
			          },
			          {
			            "BRE": {
			              "_to": "1993-08-01",
			              "_from": "1990-03-16"
			            }
			          },
			          {
			            "BRR": {
			              "_to": "1994-07-01",
			              "_from": "1993-08-01"
			            }
			          },
			          {
			            "BRL": {
			              "_from": "1994-07-01"
			            }
			          }
			        ],
			        "BS": [
			          {
			            "BSD": {
			              "_from": "1966-05-25"
			            }
			          }
			        ],
			        "BT": [
			          {
			            "INR": {
			              "_from": "1907-01-01"
			            }
			          },
			          {
			            "BTN": {
			              "_from": "1974-04-16"
			            }
			          }
			        ],
			        "BU": [
			          {
			            "BUK": {
			              "_to": "1989-06-18",
			              "_from": "1952-07-01"
			            }
			          }
			        ],
			        "BV": [
			          {
			            "NOK": {
			              "_from": "1905-06-07"
			            }
			          }
			        ],
			        "BW": [
			          {
			            "ZAR": {
			              "_to": "1976-08-23",
			              "_from": "1961-02-14"
			            }
			          },
			          {
			            "BWP": {
			              "_from": "1976-08-23"
			            }
			          }
			        ],
			        "BY": [
			          {
			            "SUR": {
			              "_to": "1991-12-25",
			              "_from": "1961-01-01"
			            }
			          },
			          {
			            "RUR": {
			              "_to": "1994-11-08",
			              "_from": "1991-12-25"
			            }
			          },
			          {
			            "BYB": {
			              "_to": "2000-12-31",
			              "_from": "1994-08-01"
			            }
			          },
			          {
			            "BYR": {
			              "_from": "2000-01-01"
			            }
			          }
			        ],
			        "BZ": [
			          {
			            "BZD": {
			              "_from": "1974-01-01"
			            }
			          }
			        ],
			        "CA": [
			          {
			            "CAD": {
			              "_from": "1858-01-01"
			            }
			          }
			        ],
			        "CC": [
			          {
			            "AUD": {
			              "_from": "1966-02-14"
			            }
			          }
			        ],
			        "CD": [
			          {
			            "ZRZ": {
			              "_to": "1993-11-01",
			              "_from": "1971-10-27"
			            }
			          },
			          {
			            "ZRN": {
			              "_to": "1998-07-01",
			              "_from": "1993-11-01"
			            }
			          },
			          {
			            "CDF": {
			              "_from": "1998-07-01"
			            }
			          }
			        ],
			        "CF": [
			          {
			            "XAF": {
			              "_from": "1993-01-01"
			            }
			          }
			        ],
			        "CG": [
			          {
			            "XAF": {
			              "_from": "1993-01-01"
			            }
			          }
			        ],
			        "CH": [
			          {
			            "CHE": {
			              "_tender": "false"
			            }
			          },
			          {
			            "CHW": {
			              "_tender": "false"
			            }
			          },
			          {
			            "CHF": {
			              "_from": "1799-03-17"
			            }
			          }
			        ],
			        "CI": [
			          {
			            "XOF": {
			              "_from": "1958-12-04"
			            }
			          }
			        ],
			        "CK": [
			          {
			            "NZD": {
			              "_from": "1967-07-10"
			            }
			          }
			        ],
			        "CL": [
			          {
			            "CLF": {
			              "_tender": "false"
			            }
			          },
			          {
			            "CLE": {
			              "_to": "1975-09-29",
			              "_from": "1960-01-01"
			            }
			          },
			          {
			            "CLP": {
			              "_from": "1975-09-29"
			            }
			          }
			        ],
			        "CM": [
			          {
			            "XAF": {
			              "_from": "1973-04-01"
			            }
			          }
			        ],
			        "CN": [
			          {
			            "CNY": {
			              "_from": "1953-03-01"
			            }
			          },
			          {
			            "CNX": {
			              "_to": "1998-12-31",
			              "_tender": "false",
			              "_from": "1979-01-01"
			            }
			          }
			        ],
			        "CO": [
			          {
			            "COU": {
			              "_tender": "false"
			            }
			          },
			          {
			            "COP": {
			              "_from": "1905-01-01"
			            }
			          }
			        ],
			        "CP": [
			          {
			            "XXX": {
			              "_tender": "false"
			            }
			          }
			        ],
			        "CR": [
			          {
			            "CRC": {
			              "_from": "1896-10-26"
			            }
			          }
			        ],
			        "CS": [
			          {
			            "YUM": {
			              "_to": "2002-05-15",
			              "_from": "1994-01-24"
			            }
			          },
			          {
			            "CSD": {
			              "_to": "2006-06-03",
			              "_from": "2002-05-15"
			            }
			          },
			          {
			            "EUR": {
			              "_to": "2006-06-03",
			              "_from": "2003-02-04"
			            }
			          }
			        ],
			        "CU": [
			          {
			            "CUP": {
			              "_from": "1859-01-01"
			            }
			          },
			          {
			            "USD": {
			              "_to": "1959-01-01",
			              "_from": "1899-01-01"
			            }
			          },
			          {
			            "CUC": {
			              "_from": "1994-01-01"
			            }
			          }
			        ],
			        "CV": [
			          {
			            "PTE": {
			              "_to": "1975-07-05",
			              "_from": "1911-05-22"
			            }
			          },
			          {
			            "CVE": {
			              "_from": "1914-01-01"
			            }
			          }
			        ],
			        "CW": [
			          {
			            "ANG": {
			              "_from": "2010-10-10"
			            }
			          }
			        ],
			        "CX": [
			          {
			            "AUD": {
			              "_from": "1966-02-14"
			            }
			          }
			        ],
			        "CY": [
			          {
			            "CYP": {
			              "_to": "2008-01-31",
			              "_from": "1914-09-10"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "2008-01-01"
			            }
			          }
			        ],
			        "CZ": [
			          {
			            "CSK": {
			              "_to": "1993-03-01",
			              "_from": "1953-06-01"
			            }
			          },
			          {
			            "CZK": {
			              "_from": "1993-01-01"
			            }
			          }
			        ],
			        "DD": [
			          {
			            "DDM": {
			              "_to": "1990-10-02",
			              "_from": "1948-07-20"
			            }
			          }
			        ],
			        "DE": [
			          {
			            "DEM": {
			              "_to": "2002-02-28",
			              "_from": "1948-06-20"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "DG": [
			          {
			            "USD": {
			              "_from": "1965-11-08"
			            }
			          }
			        ],
			        "DJ": [
			          {
			            "DJF": {
			              "_from": "1977-06-27"
			            }
			          }
			        ],
			        "DK": [
			          {
			            "DKK": {
			              "_from": "1873-05-27"
			            }
			          }
			        ],
			        "DM": [
			          {
			            "XCD": {
			              "_from": "1965-10-06"
			            }
			          }
			        ],
			        "DO": [
			          {
			            "USD": {
			              "_to": "1947-10-01",
			              "_from": "1905-06-21"
			            }
			          },
			          {
			            "DOP": {
			              "_from": "1947-10-01"
			            }
			          }
			        ],
			        "DZ": [
			          {
			            "DZD": {
			              "_from": "1964-04-01"
			            }
			          }
			        ],
			        "EA": [
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "EC": [
			          {
			            "ECS": {
			              "_to": "2000-10-02",
			              "_from": "1884-04-01"
			            }
			          },
			          {
			            "ECV": {
			              "_to": "2000-01-09",
			              "_tender": "false",
			              "_from": "1993-05-23"
			            }
			          },
			          {
			            "USD": {
			              "_from": "2000-10-02"
			            }
			          }
			        ],
			        "EE": [
			          {
			            "SUR": {
			              "_to": "1992-06-20",
			              "_from": "1961-01-01"
			            }
			          },
			          {
			            "EEK": {
			              "_to": "2010-12-31",
			              "_from": "1992-06-21"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "2011-01-01"
			            }
			          }
			        ],
			        "EG": [
			          {
			            "EGP": {
			              "_from": "1885-11-14"
			            }
			          }
			        ],
			        "EH": [
			          {
			            "MAD": {
			              "_from": "1976-02-26"
			            }
			          }
			        ],
			        "ER": [
			          {
			            "ETB": {
			              "_to": "1997-11-08",
			              "_from": "1993-05-24"
			            }
			          },
			          {
			            "ERN": {
			              "_from": "1997-11-08"
			            }
			          }
			        ],
			        "ES": [
			          {
			            "ESP": {
			              "_to": "2002-02-28",
			              "_from": "1868-10-19"
			            }
			          },
			          {
			            "ESB": {
			              "_to": "1994-12-31",
			              "_tender": "false",
			              "_from": "1975-01-01"
			            }
			          },
			          {
			            "ESA": {
			              "_to": "1981-12-31",
			              "_tender": "false",
			              "_from": "1978-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "ET": [
			          {
			            "ETB": {
			              "_from": "1976-09-15"
			            }
			          }
			        ],
			        "EU": [
			          {
			            "XEU": {
			              "_to": "1998-12-31",
			              "_tender": "false",
			              "_from": "1979-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "FI": [
			          {
			            "FIM": {
			              "_to": "2002-02-28",
			              "_from": "1963-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "FJ": [
			          {
			            "FJD": {
			              "_from": "1969-01-13"
			            }
			          }
			        ],
			        "FK": [
			          {
			            "FKP": {
			              "_from": "1901-01-01"
			            }
			          }
			        ],
			        "FM": [
			          {
			            "JPY": {
			              "_to": "1944-01-01",
			              "_from": "1914-10-03"
			            }
			          },
			          {
			            "USD": {
			              "_from": "1944-01-01"
			            }
			          }
			        ],
			        "FO": [
			          {
			            "DKK": {
			              "_from": "1948-01-01"
			            }
			          }
			        ],
			        "FR": [
			          {
			            "FRF": {
			              "_to": "2002-02-17",
			              "_from": "1960-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "GA": [
			          {
			            "XAF": {
			              "_from": "1993-01-01"
			            }
			          }
			        ],
			        "GB": [
			          {
			            "GBP": {
			              "_from": "1694-07-27"
			            }
			          }
			        ],
			        "GD": [
			          {
			            "XCD": {
			              "_from": "1967-02-27"
			            }
			          }
			        ],
			        "GE": [
			          {
			            "SUR": {
			              "_to": "1991-12-25",
			              "_from": "1961-01-01"
			            }
			          },
			          {
			            "RUR": {
			              "_to": "1993-06-11",
			              "_from": "1991-12-25"
			            }
			          },
			          {
			            "GEK": {
			              "_to": "1995-09-25",
			              "_from": "1993-04-05"
			            }
			          },
			          {
			            "GEL": {
			              "_from": "1995-09-23"
			            }
			          }
			        ],
			        "GF": [
			          {
			            "FRF": {
			              "_to": "2002-02-17",
			              "_from": "1960-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "GG": [
			          {
			            "GBP": {
			              "_from": "1830-01-01"
			            }
			          }
			        ],
			        "GH": [
			          {
			            "GHC": {
			              "_to": "2007-12-31",
			              "_from": "1979-03-09"
			            }
			          },
			          {
			            "GHS": {
			              "_from": "2007-07-03"
			            }
			          }
			        ],
			        "GI": [
			          {
			            "GIP": {
			              "_from": "1713-01-01"
			            }
			          }
			        ],
			        "GL": [
			          {
			            "DKK": {
			              "_from": "1873-05-27"
			            }
			          }
			        ],
			        "GM": [
			          {
			            "GMD": {
			              "_from": "1971-07-01"
			            }
			          }
			        ],
			        "GN": [
			          {
			            "GNS": {
			              "_to": "1986-01-06",
			              "_from": "1972-10-02"
			            }
			          },
			          {
			            "GNF": {
			              "_from": "1986-01-06"
			            }
			          }
			        ],
			        "GP": [
			          {
			            "FRF": {
			              "_to": "2002-02-17",
			              "_from": "1960-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "GQ": [
			          {
			            "GQE": {
			              "_to": "1986-06-01",
			              "_from": "1975-07-07"
			            }
			          },
			          {
			            "XAF": {
			              "_from": "1993-01-01"
			            }
			          }
			        ],
			        "GR": [
			          {
			            "GRD": {
			              "_to": "2002-02-28",
			              "_from": "1954-05-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "2001-01-01"
			            }
			          }
			        ],
			        "GS": [
			          {
			            "GBP": {
			              "_from": "1908-01-01"
			            }
			          }
			        ],
			        "GT": [
			          {
			            "GTQ": {
			              "_from": "1925-05-27"
			            }
			          }
			        ],
			        "GU": [
			          {
			            "USD": {
			              "_from": "1944-08-21"
			            }
			          }
			        ],
			        "GW": [
			          {
			            "GWE": {
			              "_to": "1976-02-28",
			              "_from": "1914-01-01"
			            }
			          },
			          {
			            "GWP": {
			              "_to": "1997-03-31",
			              "_from": "1976-02-28"
			            }
			          },
			          {
			            "XOF": {
			              "_from": "1997-03-31"
			            }
			          }
			        ],
			        "GY": [
			          {
			            "GYD": {
			              "_from": "1966-05-26"
			            }
			          }
			        ],
			        "HK": [
			          {
			            "HKD": {
			              "_from": "1895-02-02"
			            }
			          }
			        ],
			        "HM": [
			          {
			            "AUD": {
			              "_from": "1967-02-16"
			            }
			          }
			        ],
			        "HN": [
			          {
			            "HNL": {
			              "_from": "1926-04-03"
			            }
			          }
			        ],
			        "HR": [
			          {
			            "YUD": {
			              "_to": "1990-01-01",
			              "_from": "1966-01-01"
			            }
			          },
			          {
			            "YUN": {
			              "_to": "1991-12-23",
			              "_from": "1990-01-01"
			            }
			          },
			          {
			            "HRD": {
			              "_to": "1995-01-01",
			              "_from": "1991-12-23"
			            }
			          },
			          {
			            "HRK": {
			              "_from": "1994-05-30"
			            }
			          }
			        ],
			        "HT": [
			          {
			            "HTG": {
			              "_from": "1872-08-26"
			            }
			          },
			          {
			            "USD": {
			              "_from": "1915-01-01"
			            }
			          }
			        ],
			        "HU": [
			          {
			            "HUF": {
			              "_from": "1946-07-23"
			            }
			          }
			        ],
			        "IC": [
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "ID": [
			          {
			            "IDR": {
			              "_from": "1965-12-13"
			            }
			          }
			        ],
			        "IE": [
			          {
			            "GBP": {
			              "_to": "1922-01-01",
			              "_from": "1800-01-01"
			            }
			          },
			          {
			            "IEP": {
			              "_to": "2002-02-09",
			              "_from": "1922-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "IL": [
			          {
			            "ILP": {
			              "_to": "1980-02-22",
			              "_from": "1948-08-16"
			            }
			          },
			          {
			            "ILR": {
			              "_to": "1985-09-04",
			              "_from": "1980-02-22"
			            }
			          },
			          {
			            "ILS": {
			              "_from": "1985-09-04"
			            }
			          }
			        ],
			        "IM": [
			          {
			            "GBP": {
			              "_from": "1840-01-03"
			            }
			          }
			        ],
			        "IN": [
			          {
			            "INR": {
			              "_from": "1835-08-17"
			            }
			          }
			        ],
			        "IO": [
			          {
			            "USD": {
			              "_from": "1965-11-08"
			            }
			          }
			        ],
			        "IQ": [
			          {
			            "EGP": {
			              "_to": "1931-04-19",
			              "_from": "1920-11-11"
			            }
			          },
			          {
			            "INR": {
			              "_to": "1931-04-19",
			              "_from": "1920-11-11"
			            }
			          },
			          {
			            "IQD": {
			              "_from": "1931-04-19"
			            }
			          }
			        ],
			        "IR": [
			          {
			            "IRR": {
			              "_from": "1932-05-13"
			            }
			          }
			        ],
			        "IS": [
			          {
			            "DKK": {
			              "_to": "1918-12-01",
			              "_from": "1873-05-27"
			            }
			          },
			          {
			            "ISJ": {
			              "_to": "1981-01-01",
			              "_from": "1918-12-01"
			            }
			          },
			          {
			            "ISK": {
			              "_from": "1981-01-01"
			            }
			          }
			        ],
			        "IT": [
			          {
			            "ITL": {
			              "_to": "2002-02-28",
			              "_from": "1862-08-24"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "JE": [
			          {
			            "GBP": {
			              "_from": "1837-01-01"
			            }
			          }
			        ],
			        "JM": [
			          {
			            "JMD": {
			              "_from": "1969-09-08"
			            }
			          }
			        ],
			        "JO": [
			          {
			            "JOD": {
			              "_from": "1950-07-01"
			            }
			          }
			        ],
			        "JP": [
			          {
			            "JPY": {
			              "_from": "1871-06-01"
			            }
			          }
			        ],
			        "KE": [
			          {
			            "KES": {
			              "_from": "1966-09-14"
			            }
			          }
			        ],
			        "KG": [
			          {
			            "SUR": {
			              "_to": "1991-12-25",
			              "_from": "1961-01-01"
			            }
			          },
			          {
			            "RUR": {
			              "_to": "1993-05-10",
			              "_from": "1991-12-25"
			            }
			          },
			          {
			            "KGS": {
			              "_from": "1993-05-10"
			            }
			          }
			        ],
			        "KH": [
			          {
			            "KHR": {
			              "_from": "1980-03-20"
			            }
			          }
			        ],
			        "KI": [
			          {
			            "AUD": {
			              "_from": "1966-02-14"
			            }
			          }
			        ],
			        "KM": [
			          {
			            "KMF": {
			              "_from": "1975-07-06"
			            }
			          }
			        ],
			        "KN": [
			          {
			            "XCD": {
			              "_from": "1965-10-06"
			            }
			          }
			        ],
			        "KP": [
			          {
			            "KPW": {
			              "_from": "1959-04-17"
			            }
			          }
			        ],
			        "KR": [
			          {
			            "KRO": {
			              "_to": "1953-02-15",
			              "_from": "1945-08-15"
			            }
			          },
			          {
			            "KRH": {
			              "_to": "1962-06-10",
			              "_from": "1953-02-15"
			            }
			          },
			          {
			            "KRW": {
			              "_from": "1962-06-10"
			            }
			          }
			        ],
			        "KW": [
			          {
			            "KWD": {
			              "_from": "1961-04-01"
			            }
			          }
			        ],
			        "KY": [
			          {
			            "JMD": {
			              "_to": "1971-01-01",
			              "_from": "1969-09-08"
			            }
			          },
			          {
			            "KYD": {
			              "_from": "1971-01-01"
			            }
			          }
			        ],
			        "KZ": [
			          {
			            "KZT": {
			              "_from": "1993-11-05"
			            }
			          }
			        ],
			        "LA": [
			          {
			            "LAK": {
			              "_from": "1979-12-10"
			            }
			          }
			        ],
			        "LB": [
			          {
			            "LBP": {
			              "_from": "1948-02-02"
			            }
			          }
			        ],
			        "LC": [
			          {
			            "XCD": {
			              "_from": "1965-10-06"
			            }
			          }
			        ],
			        "LI": [
			          {
			            "CHF": {
			              "_from": "1921-02-01"
			            }
			          }
			        ],
			        "LK": [
			          {
			            "LKR": {
			              "_from": "1978-05-22"
			            }
			          }
			        ],
			        "LR": [
			          {
			            "LRD": {
			              "_from": "1944-01-01"
			            }
			          }
			        ],
			        "LS": [
			          {
			            "ZAR": {
			              "_from": "1961-02-14"
			            }
			          },
			          {
			            "LSL": {
			              "_from": "1980-01-22"
			            }
			          }
			        ],
			        "LT": [
			          {
			            "SUR": {
			              "_to": "1992-10-01",
			              "_from": "1961-01-01"
			            }
			          },
			          {
			            "LTT": {
			              "_to": "1993-06-25",
			              "_from": "1992-10-01"
			            }
			          },
			          {
			            "LTL": {
			              "_from": "1993-06-25"
			            }
			          }
			        ],
			        "LU": [
			          {
			            "LUF": {
			              "_to": "2002-02-28",
			              "_from": "1944-09-04"
			            }
			          },
			          {
			            "LUC": {
			              "_to": "1990-03-05",
			              "_tender": "false",
			              "_from": "1970-01-01"
			            }
			          },
			          {
			            "LUL": {
			              "_to": "1990-03-05",
			              "_tender": "false",
			              "_from": "1970-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "LV": [
			          {
			            "SUR": {
			              "_to": "1992-07-20",
			              "_from": "1961-01-01"
			            }
			          },
			          {
			            "LVR": {
			              "_to": "1993-10-17",
			              "_from": "1992-05-07"
			            }
			          },
			          {
			            "LVL": {
			              "_to": "2013-12-31",
			              "_from": "1993-06-28"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "2014-01-01"
			            }
			          }
			        ],
			        "LY": [
			          {
			            "LYD": {
			              "_from": "1971-09-01"
			            }
			          }
			        ],
			        "MA": [
			          {
			            "MAF": {
			              "_to": "1959-10-17",
			              "_from": "1881-01-01"
			            }
			          },
			          {
			            "MAD": {
			              "_from": "1959-10-17"
			            }
			          }
			        ],
			        "MC": [
			          {
			            "FRF": {
			              "_to": "2002-02-17",
			              "_from": "1960-01-01"
			            }
			          },
			          {
			            "MCF": {
			              "_to": "2002-02-17",
			              "_from": "1960-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "MD": [
			          {
			            "MDC": {
			              "_to": "1993-11-29",
			              "_from": "1992-06-01"
			            }
			          },
			          {
			            "MDL": {
			              "_from": "1993-11-29"
			            }
			          }
			        ],
			        "ME": [
			          {
			            "YUM": {
			              "_to": "2002-05-15",
			              "_from": "1994-01-24"
			            }
			          },
			          {
			            "DEM": {
			              "_to": "2002-05-15",
			              "_from": "1999-10-02"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "2002-01-01"
			            }
			          }
			        ],
			        "MF": [
			          {
			            "FRF": {
			              "_to": "2002-02-17",
			              "_from": "1960-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "MG": [
			          {
			            "MGF": {
			              "_to": "2004-12-31",
			              "_from": "1963-07-01"
			            }
			          },
			          {
			            "MGA": {
			              "_from": "1983-11-01"
			            }
			          }
			        ],
			        "MH": [
			          {
			            "USD": {
			              "_from": "1944-01-01"
			            }
			          }
			        ],
			        "MK": [
			          {
			            "MKN": {
			              "_to": "1993-05-20",
			              "_from": "1992-04-26"
			            }
			          },
			          {
			            "MKD": {
			              "_from": "1993-05-20"
			            }
			          }
			        ],
			        "ML": [
			          {
			            "XOF": {
			              "_to": "1962-07-02",
			              "_from": "1958-11-24"
			            }
			          },
			          {
			            "MLF": {
			              "_to": "1984-08-31",
			              "_from": "1962-07-02"
			            }
			          },
			          {
			            "XOF": {
			              "_from": "1984-06-01"
			            }
			          }
			        ],
			        "MM": [
			          {
			            "BUK": {
			              "_to": "1989-06-18",
			              "_from": "1952-07-01"
			            }
			          },
			          {
			            "MMK": {
			              "_from": "1989-06-18"
			            }
			          }
			        ],
			        "MN": [
			          {
			            "MNT": {
			              "_from": "1915-03-01"
			            }
			          }
			        ],
			        "MO": [
			          {
			            "MOP": {
			              "_from": "1901-01-01"
			            }
			          }
			        ],
			        "MP": [
			          {
			            "USD": {
			              "_from": "1944-01-01"
			            }
			          }
			        ],
			        "MQ": [
			          {
			            "FRF": {
			              "_to": "2002-02-17",
			              "_from": "1960-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "MR": [
			          {
			            "XOF": {
			              "_to": "1973-06-29",
			              "_from": "1958-11-28"
			            }
			          },
			          {
			            "MRO": {
			              "_from": "1973-06-29"
			            }
			          }
			        ],
			        "MS": [
			          {
			            "XCD": {
			              "_from": "1967-02-27"
			            }
			          }
			        ],
			        "MT": [
			          {
			            "MTP": {
			              "_to": "1968-06-07",
			              "_from": "1914-08-13"
			            }
			          },
			          {
			            "MTL": {
			              "_to": "2008-01-31",
			              "_from": "1968-06-07"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "2008-01-01"
			            }
			          }
			        ],
			        "MU": [
			          {
			            "MUR": {
			              "_from": "1934-04-01"
			            }
			          }
			        ],
			        "MV": [
			          {
			            "MVR": {
			              "_from": "1981-07-01"
			            }
			          }
			        ],
			        "MW": [
			          {
			            "MWK": {
			              "_from": "1971-02-15"
			            }
			          }
			        ],
			        "MX": [
			          {
			            "MXV": {
			              "_tender": "false"
			            }
			          },
			          {
			            "MXP": {
			              "_to": "1992-12-31",
			              "_from": "1822-01-01"
			            }
			          },
			          {
			            "MXN": {
			              "_from": "1993-01-01"
			            }
			          }
			        ],
			        "MY": [
			          {
			            "MYR": {
			              "_from": "1963-09-16"
			            }
			          }
			        ],
			        "MZ": [
			          {
			            "MZE": {
			              "_to": "1980-06-16",
			              "_from": "1975-06-25"
			            }
			          },
			          {
			            "MZM": {
			              "_to": "2006-12-31",
			              "_from": "1980-06-16"
			            }
			          },
			          {
			            "MZN": {
			              "_from": "2006-07-01"
			            }
			          }
			        ],
			        "NA": [
			          {
			            "ZAR": {
			              "_from": "1961-02-14"
			            }
			          },
			          {
			            "NAD": {
			              "_from": "1993-01-01"
			            }
			          }
			        ],
			        "NC": [
			          {
			            "XPF": {
			              "_from": "1985-01-01"
			            }
			          }
			        ],
			        "NE": [
			          {
			            "XOF": {
			              "_from": "1958-12-19"
			            }
			          }
			        ],
			        "NF": [
			          {
			            "AUD": {
			              "_from": "1966-02-14"
			            }
			          }
			        ],
			        "NG": [
			          {
			            "NGN": {
			              "_from": "1973-01-01"
			            }
			          }
			        ],
			        "NI": [
			          {
			            "NIC": {
			              "_to": "1991-04-30",
			              "_from": "1988-02-15"
			            }
			          },
			          {
			            "NIO": {
			              "_from": "1991-04-30"
			            }
			          }
			        ],
			        "NL": [
			          {
			            "NLG": {
			              "_to": "2002-02-28",
			              "_from": "1813-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "NO": [
			          {
			            "SEK": {
			              "_to": "1905-06-07",
			              "_from": "1873-05-27"
			            }
			          },
			          {
			            "NOK": {
			              "_from": "1905-06-07"
			            }
			          }
			        ],
			        "NP": [
			          {
			            "INR": {
			              "_to": "1966-10-17",
			              "_from": "1870-01-01"
			            }
			          },
			          {
			            "NPR": {
			              "_from": "1933-01-01"
			            }
			          }
			        ],
			        "NR": [
			          {
			            "AUD": {
			              "_from": "1966-02-14"
			            }
			          }
			        ],
			        "NU": [
			          {
			            "NZD": {
			              "_from": "1967-07-10"
			            }
			          }
			        ],
			        "NZ": [
			          {
			            "NZD": {
			              "_from": "1967-07-10"
			            }
			          }
			        ],
			        "OM": [
			          {
			            "OMR": {
			              "_from": "1972-11-11"
			            }
			          }
			        ],
			        "PA": [
			          {
			            "PAB": {
			              "_from": "1903-11-04"
			            }
			          },
			          {
			            "USD": {
			              "_from": "1903-11-18"
			            }
			          }
			        ],
			        "PE": [
			          {
			            "PES": {
			              "_to": "1985-02-01",
			              "_from": "1863-02-14"
			            }
			          },
			          {
			            "PEI": {
			              "_to": "1991-07-01",
			              "_from": "1985-02-01"
			            }
			          },
			          {
			            "PEN": {
			              "_from": "1991-07-01"
			            }
			          }
			        ],
			        "PF": [
			          {
			            "XPF": {
			              "_from": "1945-12-26"
			            }
			          }
			        ],
			        "PG": [
			          {
			            "AUD": {
			              "_to": "1975-09-16",
			              "_from": "1966-02-14"
			            }
			          },
			          {
			            "PGK": {
			              "_from": "1975-09-16"
			            }
			          }
			        ],
			        "PH": [
			          {
			            "PHP": {
			              "_from": "1946-07-04"
			            }
			          }
			        ],
			        "PK": [
			          {
			            "INR": {
			              "_to": "1947-08-15",
			              "_from": "1835-08-17"
			            }
			          },
			          {
			            "PKR": {
			              "_from": "1948-04-01"
			            }
			          }
			        ],
			        "PL": [
			          {
			            "PLZ": {
			              "_to": "1994-12-31",
			              "_from": "1950-10-28"
			            }
			          },
			          {
			            "PLN": {
			              "_from": "1995-01-01"
			            }
			          }
			        ],
			        "PM": [
			          {
			            "FRF": {
			              "_to": "2002-02-17",
			              "_from": "1972-12-21"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "PN": [
			          {
			            "NZD": {
			              "_from": "1969-01-13"
			            }
			          }
			        ],
			        "PR": [
			          {
			            "ESP": {
			              "_to": "1898-12-10",
			              "_from": "1800-01-01"
			            }
			          },
			          {
			            "USD": {
			              "_from": "1898-12-10"
			            }
			          }
			        ],
			        "PS": [
			          {
			            "JOD": {
			              "_to": "1967-06-01",
			              "_from": "1950-07-01"
			            }
			          },
			          {
			            "ILP": {
			              "_to": "1980-02-22",
			              "_from": "1967-06-01"
			            }
			          },
			          {
			            "ILS": {
			              "_from": "1985-09-04"
			            }
			          },
			          {
			            "JOD": {
			              "_from": "1996-02-12"
			            }
			          }
			        ],
			        "PT": [
			          {
			            "PTE": {
			              "_to": "2002-02-28",
			              "_from": "1911-05-22"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "PW": [
			          {
			            "USD": {
			              "_from": "1944-01-01"
			            }
			          }
			        ],
			        "PY": [
			          {
			            "PYG": {
			              "_from": "1943-11-01"
			            }
			          }
			        ],
			        "QA": [
			          {
			            "QAR": {
			              "_from": "1973-05-19"
			            }
			          }
			        ],
			        "RE": [
			          {
			            "FRF": {
			              "_to": "2002-02-17",
			              "_from": "1975-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "RO": [
			          {
			            "ROL": {
			              "_to": "2006-12-31",
			              "_from": "1952-01-28"
			            }
			          },
			          {
			            "RON": {
			              "_from": "2005-07-01"
			            }
			          }
			        ],
			        "RS": [
			          {
			            "YUM": {
			              "_to": "2002-05-15",
			              "_from": "1994-01-24"
			            }
			          },
			          {
			            "CSD": {
			              "_to": "2006-10-25",
			              "_from": "2002-05-15"
			            }
			          },
			          {
			            "RSD": {
			              "_from": "2006-10-25"
			            }
			          }
			        ],
			        "RU": [
			          {
			            "RUR": {
			              "_to": "1998-12-31",
			              "_from": "1991-12-25"
			            }
			          },
			          {
			            "RUB": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "RW": [
			          {
			            "RWF": {
			              "_from": "1964-05-19"
			            }
			          }
			        ],
			        "SA": [
			          {
			            "SAR": {
			              "_from": "1952-10-22"
			            }
			          }
			        ],
			        "SB": [
			          {
			            "AUD": {
			              "_to": "1978-06-30",
			              "_from": "1966-02-14"
			            }
			          },
			          {
			            "SBD": {
			              "_from": "1977-10-24"
			            }
			          }
			        ],
			        "SC": [
			          {
			            "SCR": {
			              "_from": "1903-11-01"
			            }
			          }
			        ],
			        "SD": [
			          {
			            "EGP": {
			              "_to": "1958-01-01",
			              "_from": "1889-01-19"
			            }
			          },
			          {
			            "GBP": {
			              "_to": "1958-01-01",
			              "_from": "1889-01-19"
			            }
			          },
			          {
			            "SDP": {
			              "_to": "1998-06-01",
			              "_from": "1957-04-08"
			            }
			          },
			          {
			            "SDD": {
			              "_to": "2007-06-30",
			              "_from": "1992-06-08"
			            }
			          },
			          {
			            "SDG": {
			              "_from": "2007-01-10"
			            }
			          }
			        ],
			        "SE": [
			          {
			            "SEK": {
			              "_from": "1873-05-27"
			            }
			          }
			        ],
			        "SG": [
			          {
			            "MYR": {
			              "_to": "1967-06-12",
			              "_from": "1963-09-16"
			            }
			          },
			          {
			            "SGD": {
			              "_from": "1967-06-12"
			            }
			          }
			        ],
			        "SH": [
			          {
			            "SHP": {
			              "_from": "1917-02-15"
			            }
			          }
			        ],
			        "SI": [
			          {
			            "SIT": {
			              "_to": "2007-01-14",
			              "_from": "1992-10-07"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "2007-01-01"
			            }
			          }
			        ],
			        "SJ": [
			          {
			            "NOK": {
			              "_from": "1905-06-07"
			            }
			          }
			        ],
			        "SK": [
			          {
			            "CSK": {
			              "_to": "1992-12-31",
			              "_from": "1953-06-01"
			            }
			          },
			          {
			            "SKK": {
			              "_to": "2009-01-01",
			              "_from": "1992-12-31"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "2009-01-01"
			            }
			          }
			        ],
			        "SL": [
			          {
			            "GBP": {
			              "_to": "1966-02-04",
			              "_from": "1808-11-30"
			            }
			          },
			          {
			            "SLL": {
			              "_from": "1964-08-04"
			            }
			          }
			        ],
			        "SM": [
			          {
			            "ITL": {
			              "_to": "2001-02-28",
			              "_from": "1865-12-23"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "SN": [
			          {
			            "XOF": {
			              "_from": "1959-04-04"
			            }
			          }
			        ],
			        "SO": [
			          {
			            "SOS": {
			              "_from": "1960-07-01"
			            }
			          }
			        ],
			        "SR": [
			          {
			            "NLG": {
			              "_to": "1940-05-10",
			              "_from": "1815-11-20"
			            }
			          },
			          {
			            "SRG": {
			              "_to": "2003-12-31",
			              "_from": "1940-05-10"
			            }
			          },
			          {
			            "SRD": {
			              "_from": "2004-01-01"
			            }
			          }
			        ],
			        "SS": [
			          {
			            "SDG": {
			              "_to": "2011-09-01",
			              "_from": "2007-01-10"
			            }
			          },
			          {
			            "SSP": {
			              "_from": "2011-07-18"
			            }
			          }
			        ],
			        "ST": [
			          {
			            "STD": {
			              "_from": "1977-09-08"
			            }
			          }
			        ],
			        "SU": [
			          {
			            "SUR": {
			              "_to": "1991-12-25",
			              "_from": "1961-01-01"
			            }
			          }
			        ],
			        "SV": [
			          {
			            "SVC": {
			              "_to": "2001-01-01",
			              "_from": "1919-11-11"
			            }
			          },
			          {
			            "USD": {
			              "_from": "2001-01-01"
			            }
			          }
			        ],
			        "SX": [
			          {
			            "ANG": {
			              "_from": "2010-10-10"
			            }
			          }
			        ],
			        "SY": [
			          {
			            "SYP": {
			              "_from": "1948-01-01"
			            }
			          }
			        ],
			        "SZ": [
			          {
			            "SZL": {
			              "_from": "1974-09-06"
			            }
			          }
			        ],
			        "TA": [
			          {
			            "GBP": {
			              "_from": "1938-01-12"
			            }
			          }
			        ],
			        "TC": [
			          {
			            "USD": {
			              "_from": "1969-09-08"
			            }
			          }
			        ],
			        "TD": [
			          {
			            "XAF": {
			              "_from": "1993-01-01"
			            }
			          }
			        ],
			        "TF": [
			          {
			            "FRF": {
			              "_to": "2002-02-17",
			              "_from": "1959-01-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "TG": [
			          {
			            "XOF": {
			              "_from": "1958-11-28"
			            }
			          }
			        ],
			        "TH": [
			          {
			            "THB": {
			              "_from": "1928-04-15"
			            }
			          }
			        ],
			        "TJ": [
			          {
			            "RUR": {
			              "_to": "1995-05-10",
			              "_from": "1991-12-25"
			            }
			          },
			          {
			            "TJR": {
			              "_to": "2000-10-25",
			              "_from": "1995-05-10"
			            }
			          },
			          {
			            "TJS": {
			              "_from": "2000-10-26"
			            }
			          }
			        ],
			        "TK": [
			          {
			            "NZD": {
			              "_from": "1967-07-10"
			            }
			          }
			        ],
			        "TL": [
			          {
			            "TPE": {
			              "_to": "2002-05-20",
			              "_from": "1959-01-02"
			            }
			          },
			          {
			            "IDR": {
			              "_to": "2002-05-20",
			              "_from": "1975-12-07"
			            }
			          },
			          {
			            "USD": {
			              "_from": "1999-10-20"
			            }
			          }
			        ],
			        "TM": [
			          {
			            "SUR": {
			              "_to": "1991-12-25",
			              "_from": "1961-01-01"
			            }
			          },
			          {
			            "RUR": {
			              "_to": "1993-11-01",
			              "_from": "1991-12-25"
			            }
			          },
			          {
			            "TMM": {
			              "_to": "2009-01-01",
			              "_from": "1993-11-01"
			            }
			          },
			          {
			            "TMT": {
			              "_from": "2009-01-01"
			            }
			          }
			        ],
			        "TN": [
			          {
			            "TND": {
			              "_from": "1958-11-01"
			            }
			          }
			        ],
			        "TO": [
			          {
			            "TOP": {
			              "_from": "1966-02-14"
			            }
			          }
			        ],
			        "TP": [
			          {
			            "TPE": {
			              "_to": "2002-05-20",
			              "_from": "1959-01-02"
			            }
			          },
			          {
			            "IDR": {
			              "_to": "2002-05-20",
			              "_from": "1975-12-07"
			            }
			          }
			        ],
			        "TR": [
			          {
			            "TRL": {
			              "_to": "2005-12-31",
			              "_from": "1922-11-01"
			            }
			          },
			          {
			            "TRY": {
			              "_from": "2005-01-01"
			            }
			          }
			        ],
			        "TT": [
			          {
			            "TTD": {
			              "_from": "1964-01-01"
			            }
			          }
			        ],
			        "TV": [
			          {
			            "AUD": {
			              "_from": "1966-02-14"
			            }
			          }
			        ],
			        "TW": [
			          {
			            "TWD": {
			              "_from": "1949-06-15"
			            }
			          }
			        ],
			        "TZ": [
			          {
			            "TZS": {
			              "_from": "1966-06-14"
			            }
			          }
			        ],
			        "UA": [
			          {
			            "SUR": {
			              "_to": "1991-12-25",
			              "_from": "1961-01-01"
			            }
			          },
			          {
			            "RUR": {
			              "_to": "1992-11-13",
			              "_from": "1991-12-25"
			            }
			          },
			          {
			            "UAK": {
			              "_to": "1993-10-17",
			              "_from": "1992-11-13"
			            }
			          },
			          {
			            "UAH": {
			              "_from": "1996-09-02"
			            }
			          }
			        ],
			        "UG": [
			          {
			            "UGS": {
			              "_to": "1987-05-15",
			              "_from": "1966-08-15"
			            }
			          },
			          {
			            "UGX": {
			              "_from": "1987-05-15"
			            }
			          }
			        ],
			        "UM": [
			          {
			            "USD": {
			              "_from": "1944-01-01"
			            }
			          }
			        ],
			        "US": [
			          {
			            "USN": {
			              "_tender": "false"
			            }
			          },
			          {
			            "USS": {
			              "_to": "2014-03-01",
			              "_tender": "false"
			            }
			          },
			          {
			            "USD": {
			              "_from": "1792-01-01"
			            }
			          }
			        ],
			        "UY": [
			          {
			            "UYI": {
			              "_tender": "false"
			            }
			          },
			          {
			            "UYP": {
			              "_to": "1993-03-01",
			              "_from": "1975-07-01"
			            }
			          },
			          {
			            "UYU": {
			              "_from": "1993-03-01"
			            }
			          }
			        ],
			        "UZ": [
			          {
			            "UZS": {
			              "_from": "1994-07-01"
			            }
			          }
			        ],
			        "VA": [
			          {
			            "ITL": {
			              "_to": "2002-02-28",
			              "_from": "1870-10-19"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "VC": [
			          {
			            "XCD": {
			              "_from": "1965-10-06"
			            }
			          }
			        ],
			        "VE": [
			          {
			            "VEB": {
			              "_to": "2008-06-30",
			              "_from": "1871-05-11"
			            }
			          },
			          {
			            "VEF": {
			              "_from": "2008-01-01"
			            }
			          }
			        ],
			        "VG": [
			          {
			            "USD": {
			              "_from": "1833-01-01"
			            }
			          },
			          {
			            "GBP": {
			              "_to": "1959-01-01",
			              "_from": "1833-01-01"
			            }
			          }
			        ],
			        "VI": [
			          {
			            "USD": {
			              "_from": "1837-01-01"
			            }
			          }
			        ],
			        "VN": [
			          {
			            "VNN": {
			              "_to": "1985-09-14",
			              "_from": "1978-05-03"
			            }
			          },
			          {
			            "VND": {
			              "_from": "1985-09-14"
			            }
			          }
			        ],
			        "VU": [
			          {
			            "VUV": {
			              "_from": "1981-01-01"
			            }
			          }
			        ],
			        "WF": [
			          {
			            "XPF": {
			              "_from": "1961-07-30"
			            }
			          }
			        ],
			        "WS": [
			          {
			            "WST": {
			              "_from": "1967-07-10"
			            }
			          }
			        ],
			        "XK": [
			          {
			            "YUM": {
			              "_to": "1999-09-30",
			              "_from": "1994-01-24"
			            }
			          },
			          {
			            "DEM": {
			              "_to": "2002-03-09",
			              "_from": "1999-09-01"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "2002-01-01"
			            }
			          }
			        ],
			        "YD": [
			          {
			            "YDD": {
			              "_to": "1996-01-01",
			              "_from": "1965-04-01"
			            }
			          }
			        ],
			        "YE": [
			          {
			            "YER": {
			              "_from": "1990-05-22"
			            }
			          }
			        ],
			        "YT": [
			          {
			            "KMF": {
			              "_to": "1976-02-23",
			              "_from": "1975-01-01"
			            }
			          },
			          {
			            "FRF": {
			              "_to": "2002-02-17",
			              "_from": "1976-02-23"
			            }
			          },
			          {
			            "EUR": {
			              "_from": "1999-01-01"
			            }
			          }
			        ],
			        "YU": [
			          {
			            "YUD": {
			              "_to": "1990-01-01",
			              "_from": "1966-01-01"
			            }
			          },
			          {
			            "YUN": {
			              "_to": "1992-07-24",
			              "_from": "1990-01-01"
			            }
			          },
			          {
			            "YUM": {
			              "_to": "2002-05-15",
			              "_from": "1994-01-24"
			            }
			          }
			        ],
			        "ZA": [
			          {
			            "ZAR": {
			              "_from": "1961-02-14"
			            }
			          },
			          {
			            "ZAL": {
			              "_to": "1995-03-13",
			              "_tender": "false",
			              "_from": "1985-09-01"
			            }
			          }
			        ],
			        "ZM": [
			          {
			            "ZMK": {
			              "_to": "2013-01-01",
			              "_from": "1968-01-16"
			            }
			          },
			          {
			            "ZMW": {
			              "_from": "2013-01-01"
			            }
			          }
			        ],
			        "ZR": [
			          {
			            "ZRZ": {
			              "_to": "1993-11-01",
			              "_from": "1971-10-27"
			            }
			          },
			          {
			            "ZRN": {
			              "_to": "1998-07-31",
			              "_from": "1993-11-01"
			            }
			          }
			        ],
			        "ZW": [
			          {
			            "RHD": {
			              "_to": "1980-04-18",
			              "_from": "1970-02-17"
			            }
			          },
			          {
			            "ZWD": {
			              "_to": "2008-08-01",
			              "_from": "1980-04-18"
			            }
			          },
			          {
			            "ZWR": {
			              "_to": "2009-02-02",
			              "_from": "2008-08-01"
			            }
			          },
			          {
			            "ZWL": {
			              "_to": "2009-04-12",
			              "_from": "2009-02-02"
			            }
			          },
			          {
			            "USD": {
			              "_from": "2009-04-12"
			            }
			          }
			        ],
			        "ZZ": [
			          {
			            "XAG": {
			              "_tender": "false"
			            }
			          },
			          {
			            "XAU": {
			              "_tender": "false"
			            }
			          },
			          {
			            "XBA": {
			              "_tender": "false"
			            }
			          },
			          {
			            "XBB": {
			              "_tender": "false"
			            }
			          },
			          {
			            "XBC": {
			              "_tender": "false"
			            }
			          },
			          {
			            "XBD": {
			              "_tender": "false"
			            }
			          },
			          {
			            "XDR": {
			              "_tender": "false"
			            }
			          },
			          {
			            "XPD": {
			              "_tender": "false"
			            }
			          },
			          {
			            "XPT": {
			              "_tender": "false"
			            }
			          },
			          {
			            "XSU": {
			              "_tender": "false"
			            }
			          },
			          {
			            "XTS": {
			              "_tender": "false"
			            }
			          },
			          {
			            "XUA": {
			              "_tender": "false"
			            }
			          },
			          {
			            "XXX": {
			              "_tender": "false"
			            }
			          },
			          {
			            "XRE": {
			              "_to": "1999-11-30",
			              "_tender": "false"
			            }
			          },
			          {
			            "XFU": {
			              "_to": "2013-11-30",
			              "_tender": "false"
			            }
			          },
			          {
			            "XFO": {
			              "_to": "2003-04-01",
			              "_tender": "false",
			              "_from": "1930-01-01"
			            }
			          }
			        ]
			      }
			    }
			  }
			};


var fakeSupplementalPluralsCatalog = {
		  "supplemental": {
			    "version": {
			      "_cldrVersion": "26",
			      "_number": "$Revision: 10807 $"
			    },
			    "generation": {
			      "_date": "$Date: 2014-08-14 14:43:27 -0500 (Thu, 14 Aug 2014) $"
			    },
			    "plurals-type-cardinal": {
			      "af": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ak": {
			        "pluralRule-count-one": "n = 0..1 @integer 0, 1 @decimal 0.0, 1.0, 0.00, 1.00, 0.000, 1.000, 0.0000, 1.0000",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.1~0.9, 1.1~1.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "am": {
			        "pluralRule-count-one": "i = 0 or n = 1 @integer 0, 1 @decimal 0.0~1.0, 0.00~0.04",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 1.1~2.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ar": {
			        "pluralRule-count-zero": "n = 0 @integer 0 @decimal 0.0, 0.00, 0.000, 0.0000",
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-two": "n = 2 @integer 2 @decimal 2.0, 2.00, 2.000, 2.0000",
			        "pluralRule-count-few": "n % 100 = 3..10 @integer 3~10, 103~110, 1003, … @decimal 3.0, 4.0, 5.0, 6.0, 7.0, 8.0, 9.0, 10.0, 103.0, 1003.0, …",
			        "pluralRule-count-many": "n % 100 = 11..99 @integer 11~26, 111, 1011, … @decimal 11.0, 12.0, 13.0, 14.0, 15.0, 16.0, 17.0, 18.0, 111.0, 1011.0, …",
			        "pluralRule-count-other": " @integer 100~102, 200~202, 300~302, 400~402, 500~502, 600, 1000, 10000, 100000, 1000000, … @decimal 0.1~0.9, 1.1~1.7, 10.1, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "asa": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ast": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "az": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "be": {
			        "pluralRule-count-one": "n % 10 = 1 and n % 100 != 11 @integer 1, 21, 31, 41, 51, 61, 71, 81, 101, 1001, … @decimal 1.0, 21.0, 31.0, 41.0, 51.0, 61.0, 71.0, 81.0, 101.0, 1001.0, …",
			        "pluralRule-count-few": "n % 10 = 2..4 and n % 100 != 12..14 @integer 2~4, 22~24, 32~34, 42~44, 52~54, 62, 102, 1002, … @decimal 2.0, 3.0, 4.0, 22.0, 23.0, 24.0, 32.0, 33.0, 102.0, 1002.0, …",
			        "pluralRule-count-many": "n % 10 = 0 or n % 10 = 5..9 or n % 100 = 11..14 @integer 0, 5~19, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0, 5.0, 6.0, 7.0, 8.0, 9.0, 10.0, 11.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …",
			        "pluralRule-count-other": "   @decimal 0.1~0.9, 1.1~1.7, 10.1, 100.1, 1000.1, …"
			      },
			      "bem": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "bez": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "bg": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "bh": {
			        "pluralRule-count-one": "n = 0..1 @integer 0, 1 @decimal 0.0, 1.0, 0.00, 1.00, 0.000, 1.000, 0.0000, 1.0000",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.1~0.9, 1.1~1.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "bm": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "bn": {
			        "pluralRule-count-one": "i = 0 or n = 1 @integer 0, 1 @decimal 0.0~1.0, 0.00~0.04",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 1.1~2.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "bo": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "br": {
			        "pluralRule-count-one": "n % 10 = 1 and n % 100 != 11,71,91 @integer 1, 21, 31, 41, 51, 61, 81, 101, 1001, … @decimal 1.0, 21.0, 31.0, 41.0, 51.0, 61.0, 81.0, 101.0, 1001.0, …",
			        "pluralRule-count-two": "n % 10 = 2 and n % 100 != 12,72,92 @integer 2, 22, 32, 42, 52, 62, 82, 102, 1002, … @decimal 2.0, 22.0, 32.0, 42.0, 52.0, 62.0, 82.0, 102.0, 1002.0, …",
			        "pluralRule-count-few": "n % 10 = 3..4,9 and n % 100 != 10..19,70..79,90..99 @integer 3, 4, 9, 23, 24, 29, 33, 34, 39, 43, 44, 49, 103, 1003, … @decimal 3.0, 4.0, 9.0, 23.0, 24.0, 29.0, 33.0, 34.0, 103.0, 1003.0, …",
			        "pluralRule-count-many": "n != 0 and n % 1000000 = 0 @integer 1000000, … @decimal 1000000.0, 1000000.00, 1000000.000, …",
			        "pluralRule-count-other": " @integer 0, 5~8, 10~20, 100, 1000, 10000, 100000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, …"
			      },
			      "brx": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "bs": {
			        "pluralRule-count-one": "v = 0 and i % 10 = 1 and i % 100 != 11 or f % 10 = 1 and f % 100 != 11 @integer 1, 21, 31, 41, 51, 61, 71, 81, 101, 1001, … @decimal 0.1, 1.1, 2.1, 3.1, 4.1, 5.1, 6.1, 7.1, 10.1, 100.1, 1000.1, …",
			        "pluralRule-count-few": "v = 0 and i % 10 = 2..4 and i % 100 != 12..14 or f % 10 = 2..4 and f % 100 != 12..14 @integer 2~4, 22~24, 32~34, 42~44, 52~54, 62, 102, 1002, … @decimal 0.2~0.4, 1.2~1.4, 2.2~2.4, 3.2~3.4, 4.2~4.4, 5.2, 10.2, 100.2, 1000.2, …",
			        "pluralRule-count-other": " @integer 0, 5~19, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0, 0.5~1.0, 1.5~2.0, 2.5~2.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ca": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "cgg": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "chr": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ckb": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "cs": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-few": "i = 2..4 and v = 0 @integer 2~4",
			        "pluralRule-count-many": "v != 0   @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …",
			        "pluralRule-count-other": " @integer 0, 5~19, 100, 1000, 10000, 100000, 1000000, …"
			      },
			      "cy": {
			        "pluralRule-count-zero": "n = 0 @integer 0 @decimal 0.0, 0.00, 0.000, 0.0000",
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-two": "n = 2 @integer 2 @decimal 2.0, 2.00, 2.000, 2.0000",
			        "pluralRule-count-few": "n = 3 @integer 3 @decimal 3.0, 3.00, 3.000, 3.0000",
			        "pluralRule-count-many": "n = 6 @integer 6 @decimal 6.0, 6.00, 6.000, 6.0000",
			        "pluralRule-count-other": " @integer 4, 5, 7~20, 100, 1000, 10000, 100000, 1000000, … @decimal 0.1~0.9, 1.1~1.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "da": {
			        "pluralRule-count-one": "n = 1 or t != 0 and i = 0,1 @integer 1 @decimal 0.1~1.6",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0, 2.0~3.4, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "de": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "dsb": {
			        "pluralRule-count-one": "v = 0 and i % 100 = 1 or f % 100 = 1 @integer 1, 101, 201, 301, 401, 501, 601, 701, 1001, … @decimal 0.1, 1.1, 2.1, 3.1, 4.1, 5.1, 6.1, 7.1, 10.1, 100.1, 1000.1, …",
			        "pluralRule-count-two": "v = 0 and i % 100 = 2 or f % 100 = 2 @integer 2, 102, 202, 302, 402, 502, 602, 702, 1002, … @decimal 0.2, 1.2, 2.2, 3.2, 4.2, 5.2, 6.2, 7.2, 10.2, 100.2, 1000.2, …",
			        "pluralRule-count-few": "v = 0 and i % 100 = 3..4 or f % 100 = 3..4 @integer 3, 4, 103, 104, 203, 204, 303, 304, 403, 404, 503, 504, 603, 604, 703, 704, 1003, … @decimal 0.3, 0.4, 1.3, 1.4, 2.3, 2.4, 3.3, 3.4, 4.3, 4.4, 5.3, 5.4, 6.3, 6.4, 7.3, 7.4, 10.3, 100.3, 1000.3, …",
			        "pluralRule-count-other": " @integer 0, 5~19, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0, 0.5~1.0, 1.5~2.0, 2.5~2.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "dv": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "dz": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ee": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "el": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "en": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "eo": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "es": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "et": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "eu": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "fa": {
			        "pluralRule-count-one": "i = 0 or n = 1 @integer 0, 1 @decimal 0.0~1.0, 0.00~0.04",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 1.1~2.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ff": {
			        "pluralRule-count-one": "i = 0,1 @integer 0, 1 @decimal 0.0~1.5",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 2.0~3.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "fi": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "fil": {
			        "pluralRule-count-one": "v = 0 and i = 1,2,3 or v = 0 and i % 10 != 4,6,9 or v != 0 and f % 10 != 4,6,9 @integer 0~3, 5, 7, 8, 10~13, 15, 17, 18, 20, 21, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.3, 0.5, 0.7, 0.8, 1.0~1.3, 1.5, 1.7, 1.8, 2.0, 2.1, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …",
			        "pluralRule-count-other": " @integer 4, 6, 9, 14, 16, 19, 24, 26, 104, 1004, … @decimal 0.4, 0.6, 0.9, 1.4, 1.6, 1.9, 2.4, 2.6, 10.4, 100.4, 1000.4, …"
			      },
			      "fo": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "fr": {
			        "pluralRule-count-one": "i = 0,1 @integer 0, 1 @decimal 0.0~1.5",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 2.0~3.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "fur": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "fy": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ga": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-two": "n = 2 @integer 2 @decimal 2.0, 2.00, 2.000, 2.0000",
			        "pluralRule-count-few": "n = 3..6 @integer 3~6 @decimal 3.0, 4.0, 5.0, 6.0, 3.00, 4.00, 5.00, 6.00, 3.000, 4.000, 5.000, 6.000, 3.0000, 4.0000, 5.0000, 6.0000",
			        "pluralRule-count-many": "n = 7..10 @integer 7~10 @decimal 7.0, 8.0, 9.0, 10.0, 7.00, 8.00, 9.00, 10.00, 7.000, 8.000, 9.000, 10.000, 7.0000, 8.0000, 9.0000, 10.0000",
			        "pluralRule-count-other": " @integer 0, 11~25, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.1, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "gd": {
			        "pluralRule-count-one": "n = 1,11 @integer 1, 11 @decimal 1.0, 11.0, 1.00, 11.00, 1.000, 11.000, 1.0000",
			        "pluralRule-count-two": "n = 2,12 @integer 2, 12 @decimal 2.0, 12.0, 2.00, 12.00, 2.000, 12.000, 2.0000",
			        "pluralRule-count-few": "n = 3..10,13..19 @integer 3~10, 13~19 @decimal 3.0, 4.0, 5.0, 6.0, 7.0, 8.0, 9.0, 10.0, 13.0, 14.0, 15.0, 16.0, 17.0, 18.0, 19.0, 3.00",
			        "pluralRule-count-other": " @integer 0, 20~34, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.1, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "gl": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "gsw": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "gu": {
			        "pluralRule-count-one": "i = 0 or n = 1 @integer 0, 1 @decimal 0.0~1.0, 0.00~0.04",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 1.1~2.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "guw": {
			        "pluralRule-count-one": "n = 0..1 @integer 0, 1 @decimal 0.0, 1.0, 0.00, 1.00, 0.000, 1.000, 0.0000, 1.0000",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.1~0.9, 1.1~1.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "gv": {
			        "pluralRule-count-one": "v = 0 and i % 10 = 1 @integer 1, 11, 21, 31, 41, 51, 61, 71, 101, 1001, …",
			        "pluralRule-count-two": "v = 0 and i % 10 = 2 @integer 2, 12, 22, 32, 42, 52, 62, 72, 102, 1002, …",
			        "pluralRule-count-few": "v = 0 and i % 100 = 0,20,40,60,80 @integer 0, 20, 40, 60, 80, 100, 120, 140, 1000, 10000, 100000, 1000000, …",
			        "pluralRule-count-many": "v != 0   @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …",
			        "pluralRule-count-other": " @integer 3~10, 13~19, 23, 103, 1003, …"
			      },
			      "ha": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "haw": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "he": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-two": "i = 2 and v = 0 @integer 2",
			        "pluralRule-count-many": "v = 0 and n != 0..10 and n % 10 = 0 @integer 20, 30, 40, 50, 60, 70, 80, 90, 100, 1000, 10000, 100000, 1000000, …",
			        "pluralRule-count-other": " @integer 0, 3~17, 101, 1001, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "hi": {
			        "pluralRule-count-one": "i = 0 or n = 1 @integer 0, 1 @decimal 0.0~1.0, 0.00~0.04",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 1.1~2.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "hr": {
			        "pluralRule-count-one": "v = 0 and i % 10 = 1 and i % 100 != 11 or f % 10 = 1 and f % 100 != 11 @integer 1, 21, 31, 41, 51, 61, 71, 81, 101, 1001, … @decimal 0.1, 1.1, 2.1, 3.1, 4.1, 5.1, 6.1, 7.1, 10.1, 100.1, 1000.1, …",
			        "pluralRule-count-few": "v = 0 and i % 10 = 2..4 and i % 100 != 12..14 or f % 10 = 2..4 and f % 100 != 12..14 @integer 2~4, 22~24, 32~34, 42~44, 52~54, 62, 102, 1002, … @decimal 0.2~0.4, 1.2~1.4, 2.2~2.4, 3.2~3.4, 4.2~4.4, 5.2, 10.2, 100.2, 1000.2, …",
			        "pluralRule-count-other": " @integer 0, 5~19, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0, 0.5~1.0, 1.5~2.0, 2.5~2.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "hsb": {
			        "pluralRule-count-one": "v = 0 and i % 100 = 1 or f % 100 = 1 @integer 1, 101, 201, 301, 401, 501, 601, 701, 1001, … @decimal 0.1, 1.1, 2.1, 3.1, 4.1, 5.1, 6.1, 7.1, 10.1, 100.1, 1000.1, …",
			        "pluralRule-count-two": "v = 0 and i % 100 = 2 or f % 100 = 2 @integer 2, 102, 202, 302, 402, 502, 602, 702, 1002, … @decimal 0.2, 1.2, 2.2, 3.2, 4.2, 5.2, 6.2, 7.2, 10.2, 100.2, 1000.2, …",
			        "pluralRule-count-few": "v = 0 and i % 100 = 3..4 or f % 100 = 3..4 @integer 3, 4, 103, 104, 203, 204, 303, 304, 403, 404, 503, 504, 603, 604, 703, 704, 1003, … @decimal 0.3, 0.4, 1.3, 1.4, 2.3, 2.4, 3.3, 3.4, 4.3, 4.4, 5.3, 5.4, 6.3, 6.4, 7.3, 7.4, 10.3, 100.3, 1000.3, …",
			        "pluralRule-count-other": " @integer 0, 5~19, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0, 0.5~1.0, 1.5~2.0, 2.5~2.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "hu": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "hy": {
			        "pluralRule-count-one": "i = 0,1 @integer 0, 1 @decimal 0.0~1.5",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 2.0~3.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "id": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ig": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ii": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "in": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "is": {
			        "pluralRule-count-one": "t = 0 and i % 10 = 1 and i % 100 != 11 or t != 0 @integer 1, 21, 31, 41, 51, 61, 71, 81, 101, 1001, … @decimal 0.1~1.6, 10.1, 100.1, 1000.1, …",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0, 2.0, 3.0, 4.0, 5.0, 6.0, 7.0, 8.0, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "it": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "iu": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-two": "n = 2 @integer 2 @decimal 2.0, 2.00, 2.000, 2.0000",
			        "pluralRule-count-other": " @integer 0, 3~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "iw": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-two": "i = 2 and v = 0 @integer 2",
			        "pluralRule-count-many": "v = 0 and n != 0..10 and n % 10 = 0 @integer 20, 30, 40, 50, 60, 70, 80, 90, 100, 1000, 10000, 100000, 1000000, …",
			        "pluralRule-count-other": " @integer 0, 3~17, 101, 1001, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ja": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "jbo": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "jgo": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ji": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "jmc": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "jv": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "jw": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ka": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "kab": {
			        "pluralRule-count-one": "i = 0,1 @integer 0, 1 @decimal 0.0~1.5",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 2.0~3.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "kaj": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "kcg": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "kde": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "kea": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "kk": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "kkj": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "kl": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "km": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "kn": {
			        "pluralRule-count-one": "i = 0 or n = 1 @integer 0, 1 @decimal 0.0~1.0, 0.00~0.04",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 1.1~2.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ko": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ks": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ksb": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ksh": {
			        "pluralRule-count-zero": "n = 0 @integer 0 @decimal 0.0, 0.00, 0.000, 0.0000",
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.1~0.9, 1.1~1.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ku": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "kw": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-two": "n = 2 @integer 2 @decimal 2.0, 2.00, 2.000, 2.0000",
			        "pluralRule-count-other": " @integer 0, 3~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ky": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "lag": {
			        "pluralRule-count-zero": "n = 0 @integer 0 @decimal 0.0, 0.00, 0.000, 0.0000",
			        "pluralRule-count-one": "i = 0,1 and n != 0 @integer 1 @decimal 0.1~1.6",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 2.0~3.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "lb": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "lg": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "lkt": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ln": {
			        "pluralRule-count-one": "n = 0..1 @integer 0, 1 @decimal 0.0, 1.0, 0.00, 1.00, 0.000, 1.000, 0.0000, 1.0000",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.1~0.9, 1.1~1.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "lo": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "lt": {
			        "pluralRule-count-one": "n % 10 = 1 and n % 100 != 11..19 @integer 1, 21, 31, 41, 51, 61, 71, 81, 101, 1001, … @decimal 1.0, 21.0, 31.0, 41.0, 51.0, 61.0, 71.0, 81.0, 101.0, 1001.0, …",
			        "pluralRule-count-few": "n % 10 = 2..9 and n % 100 != 11..19 @integer 2~9, 22~29, 102, 1002, … @decimal 2.0, 3.0, 4.0, 5.0, 6.0, 7.0, 8.0, 9.0, 22.0, 102.0, 1002.0, …",
			        "pluralRule-count-many": "f != 0   @decimal 0.1~0.9, 1.1~1.7, 10.1, 100.1, 1000.1, …",
			        "pluralRule-count-other": " @integer 0, 10~20, 30, 40, 50, 60, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0, 10.0, 11.0, 12.0, 13.0, 14.0, 15.0, 16.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "lv": {
			        "pluralRule-count-zero": "n % 10 = 0 or n % 100 = 11..19 or v = 2 and f % 100 = 11..19 @integer 0, 10~20, 30, 40, 50, 60, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0, 10.0, 11.0, 12.0, 13.0, 14.0, 15.0, 16.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …",
			        "pluralRule-count-one": "n % 10 = 1 and n % 100 != 11 or v = 2 and f % 10 = 1 and f % 100 != 11 or v != 2 and f % 10 = 1 @integer 1, 21, 31, 41, 51, 61, 71, 81, 101, 1001, … @decimal 0.1, 1.0, 1.1, 2.1, 3.1, 4.1, 5.1, 6.1, 7.1, 10.1, 100.1, 1000.1, …",
			        "pluralRule-count-other": " @integer 2~9, 22~29, 102, 1002, … @decimal 0.2~0.9, 1.2~1.9, 10.2, 100.2, 1000.2, …"
			      },
			      "mas": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "mg": {
			        "pluralRule-count-one": "n = 0..1 @integer 0, 1 @decimal 0.0, 1.0, 0.00, 1.00, 0.000, 1.000, 0.0000, 1.0000",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.1~0.9, 1.1~1.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "mgo": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "mk": {
			        "pluralRule-count-one": "v = 0 and i % 10 = 1 or f % 10 = 1 @integer 1, 11, 21, 31, 41, 51, 61, 71, 101, 1001, … @decimal 0.1, 1.1, 2.1, 3.1, 4.1, 5.1, 6.1, 7.1, 10.1, 100.1, 1000.1, …",
			        "pluralRule-count-other": " @integer 0, 2~10, 12~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0, 0.2~1.0, 1.2~1.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ml": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "mn": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "mo": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-few": "v != 0 or n = 0 or n != 1 and n % 100 = 1..19 @integer 0, 2~16, 101, 1001, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …",
			        "pluralRule-count-other": " @integer 20~35, 100, 1000, 10000, 100000, 1000000, …"
			      },
			      "mr": {
			        "pluralRule-count-one": "i = 0 or n = 1 @integer 0, 1 @decimal 0.0~1.0, 0.00~0.04",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 1.1~2.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ms": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "mt": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-few": "n = 0 or n % 100 = 2..10 @integer 0, 2~10, 102~107, 1002, … @decimal 0.0, 2.0, 3.0, 4.0, 5.0, 6.0, 7.0, 8.0, 10.0, 102.0, 1002.0, …",
			        "pluralRule-count-many": "n % 100 = 11..19 @integer 11~19, 111~117, 1011, … @decimal 11.0, 12.0, 13.0, 14.0, 15.0, 16.0, 17.0, 18.0, 111.0, 1011.0, …",
			        "pluralRule-count-other": " @integer 20~35, 100, 1000, 10000, 100000, 1000000, … @decimal 0.1~0.9, 1.1~1.7, 10.1, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "my": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "nah": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "naq": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-two": "n = 2 @integer 2 @decimal 2.0, 2.00, 2.000, 2.0000",
			        "pluralRule-count-other": " @integer 0, 3~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "nb": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "nd": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ne": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "nl": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "nn": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "nnh": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "no": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "nqo": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "nr": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "nso": {
			        "pluralRule-count-one": "n = 0..1 @integer 0, 1 @decimal 0.0, 1.0, 0.00, 1.00, 0.000, 1.000, 0.0000, 1.0000",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.1~0.9, 1.1~1.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ny": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "nyn": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "om": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "or": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "os": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "pa": {
			        "pluralRule-count-one": "n = 0..1 @integer 0, 1 @decimal 0.0, 1.0, 0.00, 1.00, 0.000, 1.000, 0.0000, 1.0000",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.1~0.9, 1.1~1.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "pap": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "pl": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-few": "v = 0 and i % 10 = 2..4 and i % 100 != 12..14 @integer 2~4, 22~24, 32~34, 42~44, 52~54, 62, 102, 1002, …",
			        "pluralRule-count-many": "v = 0 and i != 1 and i % 10 = 0..1 or v = 0 and i % 10 = 5..9 or v = 0 and i % 100 = 12..14 @integer 0, 5~19, 100, 1000, 10000, 100000, 1000000, …",
			        "pluralRule-count-other": "   @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "prg": {
			        "pluralRule-count-zero": "n % 10 = 0 or n % 100 = 11..19 or v = 2 and f % 100 = 11..19 @integer 0, 10~20, 30, 40, 50, 60, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0, 10.0, 11.0, 12.0, 13.0, 14.0, 15.0, 16.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …",
			        "pluralRule-count-one": "n % 10 = 1 and n % 100 != 11 or v = 2 and f % 10 = 1 and f % 100 != 11 or v != 2 and f % 10 = 1 @integer 1, 21, 31, 41, 51, 61, 71, 81, 101, 1001, … @decimal 0.1, 1.0, 1.1, 2.1, 3.1, 4.1, 5.1, 6.1, 7.1, 10.1, 100.1, 1000.1, …",
			        "pluralRule-count-other": " @integer 2~9, 22~29, 102, 1002, … @decimal 0.2~0.9, 1.2~1.9, 10.2, 100.2, 1000.2, …"
			      },
			      "ps": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "pt": {
			        "pluralRule-count-one": "n = 0..2 and n != 2 @integer 0, 1 @decimal 0.0, 1.0, 0.00, 1.00, 0.000, 1.000, 0.0000, 1.0000",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.1~0.9, 1.1~1.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "pt-PT": {
			        "pluralRule-count-one": "n = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "rm": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ro": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-few": "v != 0 or n = 0 or n != 1 and n % 100 = 1..19 @integer 0, 2~16, 101, 1001, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …",
			        "pluralRule-count-other": " @integer 20~35, 100, 1000, 10000, 100000, 1000000, …"
			      },
			      "rof": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "root": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ru": {
			        "pluralRule-count-one": "v = 0 and i % 10 = 1 and i % 100 != 11 @integer 1, 21, 31, 41, 51, 61, 71, 81, 101, 1001, …",
			        "pluralRule-count-few": "v = 0 and i % 10 = 2..4 and i % 100 != 12..14 @integer 2~4, 22~24, 32~34, 42~44, 52~54, 62, 102, 1002, …",
			        "pluralRule-count-many": "v = 0 and i % 10 = 0 or v = 0 and i % 10 = 5..9 or v = 0 and i % 100 = 11..14 @integer 0, 5~19, 100, 1000, 10000, 100000, 1000000, …",
			        "pluralRule-count-other": "   @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "rwk": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "sah": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "saq": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "se": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-two": "n = 2 @integer 2 @decimal 2.0, 2.00, 2.000, 2.0000",
			        "pluralRule-count-other": " @integer 0, 3~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "seh": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ses": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "sg": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "sh": {
			        "pluralRule-count-one": "v = 0 and i % 10 = 1 and i % 100 != 11 or f % 10 = 1 and f % 100 != 11 @integer 1, 21, 31, 41, 51, 61, 71, 81, 101, 1001, … @decimal 0.1, 1.1, 2.1, 3.1, 4.1, 5.1, 6.1, 7.1, 10.1, 100.1, 1000.1, …",
			        "pluralRule-count-few": "v = 0 and i % 10 = 2..4 and i % 100 != 12..14 or f % 10 = 2..4 and f % 100 != 12..14 @integer 2~4, 22~24, 32~34, 42~44, 52~54, 62, 102, 1002, … @decimal 0.2~0.4, 1.2~1.4, 2.2~2.4, 3.2~3.4, 4.2~4.4, 5.2, 10.2, 100.2, 1000.2, …",
			        "pluralRule-count-other": " @integer 0, 5~19, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0, 0.5~1.0, 1.5~2.0, 2.5~2.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "shi": {
			        "pluralRule-count-one": "i = 0 or n = 1 @integer 0, 1 @decimal 0.0~1.0, 0.00~0.04",
			        "pluralRule-count-few": "n = 2..10 @integer 2~10 @decimal 2.0, 3.0, 4.0, 5.0, 6.0, 7.0, 8.0, 9.0, 10.0, 2.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00",
			        "pluralRule-count-other": " @integer 11~26, 100, 1000, 10000, 100000, 1000000, … @decimal 1.1~1.9, 2.1~2.7, 10.1, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "si": {
			        "pluralRule-count-one": "n = 0,1 or i = 0 and f = 1 @integer 0, 1 @decimal 0.0, 0.1, 1.0, 0.00, 0.01, 1.00, 0.000, 0.001, 1.000, 0.0000, 0.0001, 1.0000",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.2~0.9, 1.1~1.8, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "sk": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-few": "i = 2..4 and v = 0 @integer 2~4",
			        "pluralRule-count-many": "v != 0   @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …",
			        "pluralRule-count-other": " @integer 0, 5~19, 100, 1000, 10000, 100000, 1000000, …"
			      },
			      "sl": {
			        "pluralRule-count-one": "v = 0 and i % 100 = 1 @integer 1, 101, 201, 301, 401, 501, 601, 701, 1001, …",
			        "pluralRule-count-two": "v = 0 and i % 100 = 2 @integer 2, 102, 202, 302, 402, 502, 602, 702, 1002, …",
			        "pluralRule-count-few": "v = 0 and i % 100 = 3..4 or v != 0 @integer 3, 4, 103, 104, 203, 204, 303, 304, 403, 404, 503, 504, 603, 604, 703, 704, 1003, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …",
			        "pluralRule-count-other": " @integer 0, 5~19, 100, 1000, 10000, 100000, 1000000, …"
			      },
			      "sma": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-two": "n = 2 @integer 2 @decimal 2.0, 2.00, 2.000, 2.0000",
			        "pluralRule-count-other": " @integer 0, 3~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "smi": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-two": "n = 2 @integer 2 @decimal 2.0, 2.00, 2.000, 2.0000",
			        "pluralRule-count-other": " @integer 0, 3~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "smj": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-two": "n = 2 @integer 2 @decimal 2.0, 2.00, 2.000, 2.0000",
			        "pluralRule-count-other": " @integer 0, 3~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "smn": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-two": "n = 2 @integer 2 @decimal 2.0, 2.00, 2.000, 2.0000",
			        "pluralRule-count-other": " @integer 0, 3~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "sms": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-two": "n = 2 @integer 2 @decimal 2.0, 2.00, 2.000, 2.0000",
			        "pluralRule-count-other": " @integer 0, 3~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "sn": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "so": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "sq": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "sr": {
			        "pluralRule-count-one": "v = 0 and i % 10 = 1 and i % 100 != 11 or f % 10 = 1 and f % 100 != 11 @integer 1, 21, 31, 41, 51, 61, 71, 81, 101, 1001, … @decimal 0.1, 1.1, 2.1, 3.1, 4.1, 5.1, 6.1, 7.1, 10.1, 100.1, 1000.1, …",
			        "pluralRule-count-few": "v = 0 and i % 10 = 2..4 and i % 100 != 12..14 or f % 10 = 2..4 and f % 100 != 12..14 @integer 2~4, 22~24, 32~34, 42~44, 52~54, 62, 102, 1002, … @decimal 0.2~0.4, 1.2~1.4, 2.2~2.4, 3.2~3.4, 4.2~4.4, 5.2, 10.2, 100.2, 1000.2, …",
			        "pluralRule-count-other": " @integer 0, 5~19, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0, 0.5~1.0, 1.5~2.0, 2.5~2.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ss": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ssy": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "st": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "sv": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "sw": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "syr": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ta": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "te": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "teo": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "th": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ti": {
			        "pluralRule-count-one": "n = 0..1 @integer 0, 1 @decimal 0.0, 1.0, 0.00, 1.00, 0.000, 1.000, 0.0000, 1.0000",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.1~0.9, 1.1~1.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "tig": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "tk": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "tl": {
			        "pluralRule-count-one": "v = 0 and i = 1,2,3 or v = 0 and i % 10 != 4,6,9 or v != 0 and f % 10 != 4,6,9 @integer 0~3, 5, 7, 8, 10~13, 15, 17, 18, 20, 21, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.3, 0.5, 0.7, 0.8, 1.0~1.3, 1.5, 1.7, 1.8, 2.0, 2.1, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …",
			        "pluralRule-count-other": " @integer 4, 6, 9, 14, 16, 19, 24, 26, 104, 1004, … @decimal 0.4, 0.6, 0.9, 1.4, 1.6, 1.9, 2.4, 2.6, 10.4, 100.4, 1000.4, …"
			      },
			      "tn": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "to": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "tr": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ts": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "tzm": {
			        "pluralRule-count-one": "n = 0..1 or n = 11..99 @integer 0, 1, 11~24 @decimal 0.0, 1.0, 11.0, 12.0, 13.0, 14.0, 15.0, 16.0, 17.0, 18.0, 19.0, 20.0, 21.0, 22.0, 23.0, 24.0",
			        "pluralRule-count-other": " @integer 2~10, 100~106, 1000, 10000, 100000, 1000000, … @decimal 0.1~0.9, 1.1~1.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ug": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "uk": {
			        "pluralRule-count-one": "v = 0 and i % 10 = 1 and i % 100 != 11 @integer 1, 21, 31, 41, 51, 61, 71, 81, 101, 1001, …",
			        "pluralRule-count-few": "v = 0 and i % 10 = 2..4 and i % 100 != 12..14 @integer 2~4, 22~24, 32~34, 42~44, 52~54, 62, 102, 1002, …",
			        "pluralRule-count-many": "v = 0 and i % 10 = 0 or v = 0 and i % 10 = 5..9 or v = 0 and i % 100 = 11..14 @integer 0, 5~19, 100, 1000, 10000, 100000, 1000000, …",
			        "pluralRule-count-other": "   @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ur": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "uz": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "ve": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "vi": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "vo": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "vun": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "wa": {
			        "pluralRule-count-one": "n = 0..1 @integer 0, 1 @decimal 0.0, 1.0, 0.00, 1.00, 0.000, 1.000, 0.0000, 1.0000",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 0.1~0.9, 1.1~1.7, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "wae": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "wo": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "xh": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "xog": {
			        "pluralRule-count-one": "n = 1 @integer 1 @decimal 1.0, 1.00, 1.000, 1.0000",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~0.9, 1.1~1.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "yi": {
			        "pluralRule-count-one": "i = 1 and v = 0 @integer 1",
			        "pluralRule-count-other": " @integer 0, 2~16, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "yo": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "zh": {
			        "pluralRule-count-other": " @integer 0~15, 100, 1000, 10000, 100000, 1000000, … @decimal 0.0~1.5, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      },
			      "zu": {
			        "pluralRule-count-one": "i = 0 or n = 1 @integer 0, 1 @decimal 0.0~1.0, 0.00~0.04",
			        "pluralRule-count-other": " @integer 2~17, 100, 1000, 10000, 100000, 1000000, … @decimal 1.1~2.6, 10.0, 100.0, 1000.0, 10000.0, 100000.0, 1000000.0, …"
			      }
			    }
			  }
			};


describe("CLDR fake catalogs", function() {
	it("can be loaded.", function() {
		expect(true).toEqual(true);
	});
});
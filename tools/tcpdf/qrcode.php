<?php
//============================================================+
// File name   : qrcode.php
// Version     : 1.0.009
// Begin       : 2010-03-22
// Last Update : 2010-12-16
// Author      : Nicola Asuni - Tecnick.com S.r.l - Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
// Copyright (C) 2010-2010  Nicola Asuni - Tecnick.com S.r.l.
//
// This file is part of TCPDF software library.
//
// TCPDF is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// TCPDF is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with TCPDF.  If not, see <http://www.gnu.org/licenses/>.
//
// See LICENSE.TXT file for more information.
// -------------------------------------------------------------------
//
// DESCRIPTION :
//
// Class to create QR-code arrays for TCPDF class.
// QR Code symbol is a 2D barcode that can be scanned by
// handy terminals such as a mobile phone with CCD.
// The capacity of QR Code is up to 7000 digits or 4000
// characters, and has high robustness.
// This class supports QR Code model 2, described in
// JIS (Japanese Industrial Standards) X0510:2004
// or ISO/IEC 18004.
// Currently the following features are not supported:
// ECI and FNC1 mode, Micro QR Code, QR Code model 1,
// Structured mode.
//
// This class is derived from the following projects:
// ---------------------------------------------------------
// "PHP QR Code encoder"
// License: GNU-LGPLv3
// Copyright (C) 2010 by Dominik Dzienia <deltalab at poczta dot fm>
// http://phpqrcode.sourceforge.net/
// https://sourceforge.net/projects/phpqrcode/
//
// The "PHP QR Code encoder" is based on
// "C libqrencode library" (ver. 3.1.1)
// License: GNU-LGPL 2.1
// Copyright (C) 2006-2010 by Kentaro Fukuchi
// http://megaui.net/fukuchi/works/qrencode/index.en.html
//
// Reed-Solomon code encoder is written by Phil Karn, KA9Q.
// Copyright (C) 2002-2006 Phil Karn, KA9Q
//
// QR Code is registered trademark of DENSO WAVE INCORPORATED
// http://www.denso-wave.com/qrcode/index-e.html
// ---------------------------------------------------------
//============================================================+

**
* @file
* Class to create QR-code arrays for TCPDF class.
* QR Code symbol is a 2D barcode that can be scanned by handy terminals such as a mobile phone with CCD.
* The capacity of QR Code is up to 7000 digits or 4000 characters, and has high robustness.
* This class supports QR Code model 2, described in JIS (Japanese Industrial Standards) X0510:2004 or ISO/IEC 18004.
* Currently the following features are not supported: ECI and FNC1 mode, Micro QR Code, QR Code model 1, Structured mode.
*
* This class is derived from "PHP QR Code encoder" by Dominik Dzienia (http://phpqrcode.sourceforge.net/) based on "libqrencode C library 3.1.1." by Kentaro Fukuchi (http://megaui.net/fukuchi/works/qrencode/index.en.html), contains Reed-Solomon code written by Phil Karn, KA9Q. QR Code is registered trademark of DENSO WAVE INCORPORATED (http://www.denso-wave.com/qrcode/index-e.html).
* Please read comments on this class source file for full copyright and license information.
*
* @package com.tecnick.tcpdf
* @author Nicola Asuni
* @version 1.0.009
*/


/**
 * @class QRcode
 * Class to create QR-code arrays for TCPDF class.
 * QR Code symbol is a 2D barcode that can be scanned by handy terminals such as a mobile phone with CCD.
 * The capacity of QR Code is up to 7000 digits or 4000 characters, and has high robustness.
 * This class supports QR Code model 2, described in JIS (Japanese Industrial Standards) X0510:2004 or ISO/IEC 18004.
 * Currently the following features are not supported: ECI and FNC1 mode, Micro QR Code, QR Code model 1, Structured mode.
 *
 * This class is derived from "PHP QR Code encoder" by Dominik Dzienia (http://phpqrcode.sourceforge.net/) based on "libqrencode C library 3.1.1." by Kentaro Fukuchi (http://megaui.net/fukuchi/works/qrencode/index.en.html), contains Reed-Solomon code written by Phil Karn, KA9Q. QR Code is registered trademark of DENSO WAVE INCORPORATED (http://www.denso-wave.com/qrcode/index-e.html).
 * Please read comments on this class source file for full copyright and license information.
 *
 * @package com.tecnick.tcpdf
 * @author Nicola Asuni
 * @version 1.0.009
 */
class QRcode {

} // end QRcode class

//============================================================+
// END OF FILE
//============================================================+


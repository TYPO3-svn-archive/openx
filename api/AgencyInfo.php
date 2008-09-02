<?php

/*
+---------------------------------------------------------------------------+
| OpenX v2.6                                                                |
| ==========                                                                |
|                                                                           |
| Copyright (c) 2003-2008 OpenX Limited                                     |
| For contact details, see: http://www.openx.org/                           |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id:$
*/

/**
 * @package    OpenXDll
 * @author     Andriy Petlyovanyy <apetlyovanyy@lohika.com>
 *
 * This file describes the AgencyInfo class.
 *
 */

// Require the base info class.
require_once 'Info.php';

/**
 *  The agencyInfo class extends the base Info class and contains information about the agency.
 *
 */

class tx_OpenxDllAgencyInfo extends tx_OpenxInfo
{
    /**
     * The agencyID variable is the unique ID for the agency.
     *
     * @var integer $agencyId
     */
    var $agencyId;

    /**
     * This field contains the ID of the agency account.
     *
     * @var integer $accountId
     */
    var $accountId;

    /**
     * The agencycName variable is the name of the agency.
     *
     * @var string $agencyName
     */
    var $agencyName;

    /**
     * The contactName variable is the name of the contact for the agency.
     *
     * @var string $contactName
     */
    var $contactName;

    /**
     * The emailAddress variable is the email address for the agency contact.
     *
     * @var string $emailAddress
     */
    var $emailAddress;

    function getFieldsTypes()
    {
        return array(
                    'agencyId' => 'integer',
                    'accountId' => 'integer',
                    'agencyName' => 'string',
                    'contactName' => 'string',
                    'emailAddress' => 'string'
                );
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/openx/api/AgencyInfo.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/openx/api/AgencyInfo.php']);
}
?>
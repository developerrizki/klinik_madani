<?php
function required($text)
{
    if (is_array($text)) return '';
    
    return (is_null($text) or (strlen($text) == 0)) ? 'this field is required' : '';
}

/**
 * @package     isemail
 * @author      Dominic Sayers <dominic_sayers@hotmail.com>
 * @copyright   2009 Dominic Sayers
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link        http://www.dominicsayers.com/isemail
 * @version     1.15 - Bug fix suggested by Andrew Campbell of Gloucester, MA
 */

function is_email($email, $checkDNS = false)
{
	$emailLength = strlen($email);
	
    if ($emailLength > 256) return false;   // Too long

    $atIndex = strrpos($email,'@');

    if ($atIndex === false) return false;   // No at-sign
    if ($atIndex === 0) return false;   // No local part
    if ($atIndex === $emailLength - 1) return false;   // No domain part

    $braceDepth     = 0;
    $inQuote        = false;
    $escapeThisChar = false;

    for ($i = 0; $i < $emailLength; ++$i) {
        $char = $email[$i];
        $replaceChar = false;

        if ($char === '\\') {
            $escapeThisChar = !$escapeThisChar;     // Escape the next character?
        } else {
            switch ($char) {
                case '(':
                    if ($escapeThisChar) {
                        $replaceChar = true;
                    } else {
                        if ($inQuote) {
                            $replaceChar = true;
                        } else {
                            if ($braceDepth++ > 0) $replaceChar = true;     // Increment brace depth
                        }
                    }

                break;
                case ')':
                    if ($escapeThisChar) {
                        $replaceChar = true;
                    } else {
                        if ($inQuote) {
                            $replaceChar = true;
                        } else {
                            if (--$braceDepth > 0) $replaceChar = true;     // Decrement brace depth
                            if ($braceDepth < 0) $braceDepth = 0;
                        }
                    }

                break;
                case '"':
                    if ($escapeThisChar) {
                        $replaceChar = true;
                    } else {
                        if ($braceDepth === 0) {
                            $inQuote = !$inQuote;   // Are we inside a quoted string?
                        } else {
                            $replaceChar = true;
                        }
                    }
                break;
                case '.':       // Dots don't help us either
                    if ($escapeThisChar) {
                        $replaceChar = true;
                    } else {
                        if ($braceDepth > 0) $replaceChar = true;
                    }

                break;
                default:
            }

            $escapeThisChar = false;

            if ($replaceChar) $email = (string) substr_replace($email, 'x', $i, 1); // Replace the offending character with something harmless
        }
    }

    $localPart      = substr($email, 0, $atIndex);
    $domain         = substr($email, $atIndex + 1);
    $FWS            = "(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t]+)|(?:[ \\t]+(?:(?:\\r\\n)[ \\t]+)*))";    // Folding white space
      
    $dotArray       = /*. (array[int]string) .*/ preg_split('/\\.(?=(?:[^\\"]*\\"[^\\"]*\\")*(?![^\\"]*\\"))/m', $localPart);
    $partLength     = 0;

    foreach ($dotArray as $element) {
        // Remove any leading or trailing FWS
        $element        = preg_replace("/^$FWS|$FWS\$/", '', $element);
        $elementLength  = strlen($element);

        if ($elementLength === 0) return false;   // Can't have empty element (consecutive dots or dots at the start or end)

        if ($element[0] === '(') {
            $indexBrace = strpos($element, ')');
            if ($indexBrace !== false) {
                if (preg_match('/(?<!\\\\)[\\(\\)]/', substr($element, 1, $indexBrace - 1)) > 0) {
                    return false;   // Illegal characters in comment
                }
                $element        = substr($element, $indexBrace + 1, $elementLength - $indexBrace - 1);
                $elementLength  = strlen($element);
            }
        }
               
        if ($element[$elementLength - 1] === ')') {
            $indexBrace = strrpos($element, '(');
            if ($indexBrace !== false) {
				if (preg_match('/(?<!\\\\)(?:[\\(\\)])/', substr($element, $indexBrace + 1, $elementLength - $indexBrace - 2)) > 0) {
					return false;   // Illegal characters in comment
				}
				$element        = substr($element, 0, $indexBrace);
				$elementLength  = strlen($element);
            }
        }

        // Remove any leading or trailing FWS around the element (inside any comments)
        $element = preg_replace("/^$FWS|$FWS\$/", '', $element);

        // What's left counts towards the maximum length for this part
        if ($partLength > 0) $partLength++;     // for the dot
        $partLength += strlen($element);

        // Each dot-delimited component can be an atom or a quoted string
        // (because of the obs-local-part provision)
        if (preg_match('/^"(?:.)*"$/s', $element) > 0) {
            $element = preg_replace("/(?<!\\\\)$FWS/", '', $element);
            $element = preg_replace('/\\\\\\\\/', ' ', $element);
            if (preg_match('/(?<!\\\\|^)["\\r\\n\\x00](?!$)|\\\\"$|""/', $element) > 0)     return false;   // ", CR, LF and NUL must be escaped, "" is too short
        } else {
            if ($element === '') return false;   // Dots in wrong place
            if (preg_match('/[\\x00-\\x20\\(\\)<>\\[\\]:;@\\\\,\\."]/', $element) > 0)      return false;   // These characters must be in a quoted string
        }
    }

    if ($partLength > 64) return false;     // Local part must be 64 characters or less

    if (preg_match('/^\\[(.)+]$/', $domain) === 1) {
        // It's an address-literal
        $addressLiteral = substr($domain, 1, strlen($domain) - 2);
        $matchesIP      = array();
               
        // Extract IPv4 part from the end of the address-literal (if there is one)
        if (preg_match('/\\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $addressLiteral, $matchesIP) > 0) {
            $index = strrpos($addressLiteral, $matchesIP[0]);
                       
            if ($index === 0) {
                // Nothing there except a valid IPv4 address, so...
                return true;
            } else {
                // Assume it's an attempt at a mixed address (IPv6 + IPv4)
                if ($addressLiteral[$index - 1] !== ':')        return false;   // Character preceding IPv4 address must be ':'
                if (substr($addressLiteral, 0, 5) !== 'IPv6:')  return false;   // RFC5321 section 4.1.3

                $IPv6           = substr($addressLiteral, 5, ($index ===7) ? 2 : $index - 6);
                $groupMax       = 6;
            }
        } else {
            // It must be an attempt at pure IPv6
            if (substr($addressLiteral, 0, 5) !== 'IPv6:') return false;   // RFC5321 section 4.1.3
            $IPv6 = substr($addressLiteral, 5);
            $groupMax = 8;
        }

        $groupCount     = preg_match_all('/^[0-9a-fA-F]{0,4}|\\:[0-9a-fA-F]{0,4}|(.)/', $IPv6, $matchesIP);
        $index          = strpos($IPv6,'::');

        if ($index === false) {
            // We need exactly the right number of groups
            if ($groupCount !== $groupMax) return false;   // RFC5321 section 4.1.3
        } else {
            if ($index !== strrpos($IPv6,'::')) return false;   // More than one '::'
			$groupMax = ($index === 0 || $index === (strlen($IPv6) - 2)) ? $groupMax : $groupMax - 1;
            if ($groupCount > $groupMax) return false;   // Too many IPv6 groups in address
        }

        // Check for unmatched characters
        array_multisort($matchesIP[1], SORT_DESC);
        if ($matchesIP[1][0] !== '') return false;   // Illegal characters in address

        // It's a valid IPv6 address, so...
                return true;
        } else {
            $dotArray       = /*. (array[int]string) .*/ preg_split('/\\.(?=(?:[^\\"]*\\"[^\\"]*\\")*(?![^\\"]*\\"))/m', $domain);
            $partLength     = 0;
            $element        = ''; // Since we use $element after the foreach loop let's make sure it has a value

            if (count($dotArray) === 1)  return false;   // Mail host can't be a TLD (cite? What about localhost?)
                foreach ($dotArray as $element) {
                    // Remove any leading or trailing FWS
                    $element        = preg_replace("/^$FWS|$FWS\$/", '', $element);
                    $elementLength  = strlen($element);

                    if ($elementLength === 0) return false;   // Dots in wrong place

                    if ($element[0] === '(') {
                        $indexBrace = strpos($element, ')');
                        if ($indexBrace !== false) {
                            if (preg_match('/(?<!\\\\)[\\(\\)]/', substr($element, 1, $indexBrace - 1)) > 0) {
                                return false;   // Illegal characters in comment
                            }
                            $element        = substr($element, $indexBrace + 1, $elementLength - $indexBrace - 1);
                            $elementLength  = strlen($element);
                        }
                    }
                       
                    if ($element[$elementLength - 1] === ')') {
                        $indexBrace = strrpos($element, '(');
                        if ($indexBrace !== false) {
                            if (preg_match('/(?<!\\\\)(?:[\\(\\)])/', substr($element, $indexBrace + 1, $elementLength - $indexBrace - 2)) > 0)
                                return false;   // Illegal characters in comment

								$element        = substr($element, 0, $indexBrace);
                                $elementLength  = strlen($element);
                            }
                    }                      
       
                    // Remove any leading or trailing FWS around the element (inside any comments)
                    $element = preg_replace("/^$FWS|$FWS\$/", '', $element);
       
                    // What's left counts towards the maximum length for this part
                    if ($partLength > 0) $partLength++;     // for the dot
                    $partLength += strlen($element);

                    if ($elementLength > 63) return false;   // Label must be 63 characters or less
       
                    if (preg_match('/[\\x00-\\x20\\(\\)<>\\[\\]:;@\\\\,\\."]|^-|-$/', $element) > 0) {
                        return false;
                    }
                }

                if ($partLength > 255)                                          return false;   // Domain part must be 255 characters or less (http://tools.ietf.org/html/rfc1123#section-6.1.3.5)

                if (preg_match('/^[0-9]+$/', $element) > 0)                     return false;   // TLD can't be all-numeric (http://www.apps.ietf.org/rfc/rfc3696.html#sec-2)

                // Check DNS?
                if ($checkDNS && function_exists('checkdnsrr')) {
                        if (!(checkdnsrr($domain, 'A') || checkdnsrr($domain, 'MX'))) {
                                                                                return false;   // Domain doesn't actually exist
                        }
                }
        }

        // Eliminate all other factors, and the one which remains must be the truth.
        //      (Sherlock Holmes, The Sign of Four)
        
        return true;
}

function emailvalid($email)
{
    return (is_email($email)) ? '' : 'invalid email';
}
?>

<?php
require_once 'config.php';
require_once 'db_connect.php';

// Get the POST data from Africa's Talking
$sessionId = $_POST['sessionId'];
$serviceCode = $_POST['serviceCode'];
$phoneNumber = $_POST['phoneNumber'];
$text = $_POST['text'];

// Initialize the response
$response = "";

// Split the text into an array
$textArray = explode('*', $text);
$userLevel = count($textArray);

// Get database connection
$conn = $pdo;

// Check if user exists by phone number
function userExistsByPhone($phoneNumber, $conn) {
    // Add debugging
    error_log("Checking phone number: " . $phoneNumber);
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE phone_number = ?");
    $stmt->execute([$phoneNumber]);
    $exists = $stmt->rowCount() > 0;
    
    // Add debugging
    error_log("Phone number exists: " . ($exists ? "Yes" : "No"));
    
    return $exists;
}

// Check if user exists by phone number and return user data
function getUserByPhone($phoneNumber, $conn) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE phone_number = ? AND pin IS NOT NULL");
    $stmt->execute([$phoneNumber]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Verify user login with phone and PIN
function verifyUserLogin($phoneNumber, $pin, $conn) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE phone_number = ? AND pin = ?");
    $stmt->execute([$phoneNumber, $pin]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Register new user with PIN
function registerUser($phoneNumber, $name, $pin, $conn) {
    $stmt = $conn->prepare("INSERT INTO users (phone_number, name, pin) VALUES (?, ?, ?)");
    return $stmt->execute([$phoneNumber, $name, $pin]);
}

// Add emergency contact
function addEmergencyContact($userId, $name, $phoneNumber, $conn) {
    $stmt = $conn->prepare("INSERT INTO emergency_contacts (user_id, contact_name, contact_number) VALUES (?, ?, ?)");
    return $stmt->execute([$userId, $name, $phoneNumber]);
}

// Get user's emergency contacts
function getEmergencyContacts($userId, $conn) {
    $stmt = $conn->prepare("SELECT * FROM emergency_contacts WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Store session data (you might want to use a proper session storage)
function setSessionData($sessionId, $key, $value, $conn) {
    $stmt = $conn->prepare("INSERT INTO session_data (session_id, data_key, data_value) VALUES (?, ?, ?) 
                           ON DUPLICATE KEY UPDATE data_value = ?");
    $stmt->execute([$sessionId, $key, $value, $value]);
}

function getSessionData($sessionId, $key, $conn) {
    $stmt = $conn->prepare("SELECT data_value FROM session_data WHERE session_id = ? AND data_key = ?");
    $stmt->execute([$sessionId, $key]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['data_value'] : null;
}

// Clear session data
function clearSessionData($sessionId, $conn) {
    $stmt = $conn->prepare("DELETE FROM session_data WHERE session_id = ?");
    $stmt->execute([$sessionId]);
}

// Check for navigation commands
function isNavigationCommand($input) {
    return in_array($input, ['0', '00', '#']);
}

// Handle navigation commands
function handleNavigation($input, $sessionId, $conn) {
    if ($input == '00') {
        // Home - clear session and return to main menu
        clearSessionData($sessionId, $conn);
        return "CON Welcome to Emergency Assist\n1. Login to My Account\n2. Register New Account\n3. Emergency Access (Enter any registered phone number)\n\n00. Home";
    } else if ($input == '0') {
        // Back - implement context-aware back functionality
        return null; // Will be handled by context
    } else if ($input == '#') {
        // More options - context dependent
        return null; // Will be handled by context
    }
    return null;
}

// Add navigation footer to responses
function addNavigationFooter($response, $showBack = true, $showHome = true, $showMore = false) {
    $footer = "\n\n";
    if ($showBack) $footer .= "0. Back  ";
    if ($showHome) $footer .= "00. Home";
    if ($showMore) $footer .= "  #. More";
    return $response . $footer;
}

// === MAIN USSD LOGIC ===

if ($text == "") {
    // First time user calls - show main options
    $response = "CON Welcome to Emergency Assist\n1. Login to My Account\n2. Register New Account\n3. Emergency Access (Enter any registered phone number)\n\n00. Home";
} else {
    // Check for navigation commands first
    $lastInput = end($textArray);
    
    if ($lastInput == '00') {
        // Home command
        clearSessionData($sessionId, $conn);
        $response = "CON Welcome to Emergency Assist\n1. Login to My Account\n2. Register New Account\n3. Emergency Access (Enter any registered phone number)\n\n00. Home";
    } else {
        // Parse user input based on their selection
        
        switch ($textArray[0]) {
            case "1": // Login to My Account
                if ($userLevel == 1) {
                    // User selected login, check if this phone is registered
                    if (userExistsByPhone($phoneNumber, $conn)) {
                        $response = addNavigationFooter("CON Enter your PIN:");
                    } else {
                        $response = "END This phone number is not registered. Please register first or use Emergency Access option.";
                    }
                } else if ($userLevel == 2) {
                    if ($textArray[1] == '0') {
                        // Back to main menu
                        $response = "CON Welcome to Emergency Assist\n1. Login to My Account\n2. Register New Account\n3. Emergency Access (Enter any registered phone number)\n\n00. Home";
                    } else {
                        // User entered PIN for their own phone
                        $pin = $textArray[1];
                        $user = verifyUserLogin($phoneNumber, $pin, $conn);
                        if ($user) {
                            setSessionData($sessionId, 'logged_in_user_id', $user['id'], $conn);
                            setSessionData($sessionId, 'user_name', $user['name'], $conn);
                            $response = addNavigationFooter("CON Welcome back, " . $user['name'] . "!\n1. HELP (Show Contacts)\n2. Manage My Contacts");
                        } else {
                            $response = addNavigationFooter("CON Invalid PIN. Please try again.\nEnter your PIN:");
                        }
                    }
                } else if ($userLevel == 3) {
                    if ($textArray[2] == '0') {
                        // Back to PIN entry
                        $response = addNavigationFooter("CON Enter your PIN:");
                    } else if ($textArray[2] == '00') {
                        // Home
                        clearSessionData($sessionId, $conn);
                        $response = "CON Welcome to Emergency Assist\n1. Login to My Account\n2. Register New Account\n3. Emergency Access (Enter any registered phone number)\n\n00. Home";
                    }
                }
                break;
                
            case "2": // Register New Account
                if ($userLevel == 1) {
                    $response = addNavigationFooter("CON Enter your full name:");
                } else if ($userLevel == 2) {
                    if ($textArray[1] == '0') {
                        // Back to main menu
                        $response = "CON Welcome to Emergency Assist\n1. Login to My Account\n2. Register New Account\n3. Emergency Access (Enter any registered phone number)\n\n00. Home";
                    } else {
                        setSessionData($sessionId, 'temp_name', $textArray[1], $conn);
                        $response = addNavigationFooter("CON Create a 4-digit PIN for your account:");
                    }
                } else if ($userLevel == 3) {
                    if ($textArray[2] == '0') {
                        // Back to name entry
                        $response = addNavigationFooter("CON Enter your full name:");
                    } else {
                        $name = getSessionData($sessionId, 'temp_name', $conn);
                        $pin = $textArray[2];
                        
                        // Validate PIN (4 digits)
                        if (strlen($pin) == 4 && is_numeric($pin)) {
                            if (registerUser($phoneNumber, $name, $pin, $conn)) {
                                $response = addNavigationFooter("CON Registration successful, {$name}!\nYour PIN is: {$pin}\nLet's add your first emergency contact. Enter their name:", false);
                                // Get the newly created user
                                $user = getUserByPhone($phoneNumber, $conn);
                                setSessionData($sessionId, 'logged_in_user_id', $user['id'], $conn);
                            } else {
                                $response = "END Registration failed. Please try again.";
                            }
                        } else {
                            $response = addNavigationFooter("CON PIN must be exactly 4 digits. Please try again.\nCreate a 4-digit PIN:");
                        }
                    }
                } else if ($userLevel == 4) {
                    if ($textArray[3] == '0') {
                        // Back to PIN creation
                        $response = addNavigationFooter("CON Create a 4-digit PIN for your account:");
                    } else {
                        // Adding first contact after registration
                        setSessionData($sessionId, 'temp_contact_name', $textArray[3], $conn);
                        $response = addNavigationFooter("CON Enter contact phone number:");
                    }
                } else if ($userLevel == 5) {
                    if ($textArray[4] == '0') {
                        // Back to contact name entry
                        $response = addNavigationFooter("CON Let's add your first emergency contact. Enter their name:", false);
                    } else {
                        $userId = getSessionData($sessionId, 'logged_in_user_id', $conn);
                        $contactName = getSessionData($sessionId, 'temp_contact_name', $conn);
                        $contactNumber = $textArray[4];
                        
                        if (addEmergencyContact($userId, $contactName, $contactNumber, $conn)) {
                            $response = addNavigationFooter("CON Contact added successfully!\n1. Add Another Contact\n2. Go to Main Menu", false);
                        } else {
                            $response = addNavigationFooter("CON Failed to add contact. Try again.\nEnter contact phone number:");
                        }
                    }
                } else if ($userLevel == 6) {
                    if ($textArray[5] == '1') {
                        // Add another contact
                        $response = addNavigationFooter("CON Enter next contact name:");
                        setSessionData($sessionId, 'adding_more_contacts', 'true', $conn);
                    } else if ($textArray[5] == '2') {
                        // Go to main menu
                        $userName = getSessionData($sessionId, 'user_name', $conn);
                        $response = addNavigationFooter("CON Welcome, " . $userName . "!\n1. HELP (Show Contacts)\n2. Manage My Contacts");
                    }
                }
                break;
                
            case "3": // Emergency Access
                if ($userLevel == 1) {
                    $response = addNavigationFooter("CON Emergency Access\nEnter the registered phone number:");
                } else if ($userLevel == 2) {
                    if ($textArray[1] == '0') {
                        // Back to main menu
                        $response = "CON Welcome to Emergency Assist\n1. Login to My Account\n2. Register New Account\n3. Emergency Access (Enter any registered phone number)\n\n00. Home";
                    } else {
                        $emergencyPhone = $textArray[1];
                        // Add debugging
                        error_log("Emergency access attempt for phone: " . $emergencyPhone);
                        
                        if (userExistsByPhone($emergencyPhone, $conn)) {
                            setSessionData($sessionId, 'emergency_phone', $emergencyPhone, $conn);
                            $response = addNavigationFooter("CON Enter the PIN for {$emergencyPhone}:");
                        } else {
                            $response = addNavigationFooter("CON Phone number not found. Try again.\nEnter the registered phone number:");
                        }
                    }
                } else if ($userLevel == 3) {
                    if ($textArray[2] == '0') {
                        // Back to phone number entry
                        $response = addNavigationFooter("CON Emergency Access\nEnter the registered phone number:");
                    } else {
                        $emergencyPhone = getSessionData($sessionId, 'emergency_phone', $conn);
                        $pin = $textArray[2];
                        $user = verifyUserLogin($emergencyPhone, $pin, $conn);
                        
                        if ($user) {
                            setSessionData($sessionId, 'logged_in_user_id', $user['id'], $conn);
                            setSessionData($sessionId, 'user_name', $user['name'], $conn);
                            $response = addNavigationFooter("CON Emergency access granted for " . $user['name'] . "!\n1. HELP (Show Contacts)\n2. View My Contacts");
                        } else {
                            $response = addNavigationFooter("CON Invalid PIN. Try again.\nEnter the PIN for {$emergencyPhone}:");
                        }
                    }
                }
                break;
                
            default:
                // Handle main menu options after login (1. HELP, 2. Manage Contacts)
                $userId = getSessionData($sessionId, 'logged_in_user_id', $conn);
                
                if (!$userId) {
                    $response = "END Session expired. Please start again.";
                    break;
                }
                
                if ($textArray[0] == "1") { // HELP (Show Contacts) Flow
                    if ($userLevel == 1) {
                        $contacts = getEmergencyContacts($userId, $conn);
                        if (count($contacts) > 0) {
                            $response = "CON Who do you want to contact?\n";
                            $count = 1;
                            foreach ($contacts as $contact) {
                                $response .= $count . ". " . $contact['contact_name'] . "\n";
                                $count++;
                            }
                            $response = addNavigationFooter($response);
                        } else {
                            $response = addNavigationFooter("CON You have no emergency contacts saved.\n1. Add Contact Now\n2. Back to Menu", false);
                        }
                    } else if ($userLevel == 2) {
                        if ($textArray[1] == '0') {
                            // Back to main menu after login
                            $userName = getSessionData($sessionId, 'user_name', $conn);
                            $response = addNavigationFooter("CON Welcome, " . $userName . "!\n1. HELP (Show Contacts)\n2. Manage My Contacts");
                        } else if ($textArray[1] == '1' && getEmergencyContacts($userId, $conn) == 0) {
                            // Add contact when none exist
                            $response = addNavigationFooter("CON Enter contact name:");
                        } else {
                            // User has selected a contact
                            $selectedContactIndex = $textArray[1] - 1;
                            $contacts = getEmergencyContacts($userId, $conn);
                            if (isset($contacts[$selectedContactIndex])) {
                                $selectedContact = $contacts[$selectedContactIndex];
                                setSessionData($sessionId, 'selected_contact', json_encode($selectedContact), $conn);
                                $response = addNavigationFooter("CON Contact " . $selectedContact['contact_name'] . ".\n1. Call Now\n2. Send Distress SMS");
                            } else {
                                $response = addNavigationFooter("CON Invalid selection. Try again.\nWho do you want to contact?");
                            }
                        }
                    } else if ($userLevel == 3) {
                        if ($textArray[2] == '0') {
                            // Back to contact list
                            $contacts = getEmergencyContacts($userId, $conn);
                            $response = "CON Who do you want to contact?\n";
                            $count = 1;
                            foreach ($contacts as $contact) {
                                $response .= $count . ". " . $contact['contact_name'] . "\n";
                                $count++;
                            }
                            $response = addNavigationFooter($response);
                        } else {
                            // Emergency action
                            $selectedContact = json_decode(getSessionData($sessionId, 'selected_contact', $conn), true);
                            if ($textArray[2] == "1") {
                                $response = "END Calling " . $selectedContact['contact_name'] . " at " . $selectedContact['contact_number'] . "...";
                                // Add Africa's Talking Voice API call here
                            } else if ($textArray[2] == "2") {
                                $response = "END Sending emergency SMS to " . $selectedContact['contact_name'] . "...";
                                // Add Africa's Talking SMS API call here
                            }
                        }
                    }
                } else if ($textArray[0] == "2") { // Manage/View Contacts
                    if ($userLevel == 1) {
                        $contacts = getEmergencyContacts($userId, $conn);
                        if (count($contacts) > 0) {
                            $response = "CON Your Emergency Contacts:\n";
                            $count = 1;
                            foreach ($contacts as $contact) {
                                $response .= $count . ". " . $contact['contact_name'] . ": " . $contact['contact_number'] . "\n";
                                $count++;
                            }
                            $response = addNavigationFooter($response . "\n#. More Options", true, true, true);
                        } else {
                            $response = addNavigationFooter("CON You have no contacts saved.\n1. Add Contact Now", false);
                        }
                    } else if ($userLevel == 2) {
                        if ($textArray[1] == '0') {
                            // Back to main menu after login
                            $userName = getSessionData($sessionId, 'user_name', $conn);
                            $response = addNavigationFooter("CON Welcome, " . $userName . "!\n1. HELP (Show Contacts)\n2. Manage My Contacts");
                        } else if ($textArray[1] == '#') {
                            // More options for contact management
                            $response = addNavigationFooter("CON Contact Management:\n1. Add New Contact\n2. Edit Contact\n3. Delete Contact");
                        } else if ($textArray[1] == '1' && count(getEmergencyContacts($userId, $conn)) == 0) {
                            // Add contact when none exist
                            $response = addNavigationFooter("CON Enter contact name:");
                        }
                    } else if ($userLevel == 3) {
                        if ($textArray[1] == '#' && $textArray[2] == '1') {
                            // Add new contact
                            $response = addNavigationFooter("CON Enter new contact name:");
                        }
                        // Add more contact management options here
                    }
                }
                break;
        }
    }
}

// Print the response
header('Content-type: text/plain');
echo $response;
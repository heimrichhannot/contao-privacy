# Contao Privacy Bundle

This bundle contains functionality concerning privacy and the European Union's "General Data Protection Regulation" (GDPR, in German: "Datenschutz-Grundverordnung", DSGVO).

## Legal disclaimer

Use this bundle at your own risk. Although we as the developer try our best to design this bundle to fulfill the legal requirements we CAN'T GUARANTEE anything in terms completeness and correctness. Also we don't offer any legal consulting. We strongly encourage you to consult a lawyer if you have any questions or concerns.

## Features

- adds the new Contao entities `tl_privacy_protocol_archive` and `tl_privacy_protocol_entry` for storing privacy relevant actions like opt-ins, ...
- offers a simply API

## Installation

1. Simply install using composer: `composer require heimrichhannot/contao-privacy`
2. Update your database and clear your caches.
3. Now you have the new menu entry "privacy" in the Contao menu on the left

## Usage

### The privacy protocol

1. Add a new protocol archive and select the fields you'd like to store (CAUTION: Do NOT store personal data for which you don't have the user's permission!).
2. Choose one of the following functions for adding new entries programmatically:

#### Add a new entry from the context of a module

```php
class MyModule {
    // ...
    public function compile() {
        // this represents your function for sending the opt in email
        $success = $this->sendOptInEmail($firstname, $lastname, $email);

        // only create a protocol entry if the email has indeed been sent
        if ($success)
        {
            $protocolManager = new \HeimrichHannot\Privacy\Manager\ProtocolManager();
            $protocolManager->addEntryFromModule(
                // the type of action
                \HeimrichHannot\Privacy\Backend\ProtocolEntry::TYPE_FIRST_OPT_IN,
                // the id of your destination protocol archive
                1,
                // the data you want to add to the protocol entry to be created
                // CAUTION: Do NOT store personal data for which you don't have the user's permission!
                [
                    'firstname' => $firstname,
                    'lastname'  => $lastname,
                    'email'     => $email
                ],
                // the \Contao\Module instance you're calling from
                $this,
                // optional: composer package name of the bundle your module lives in (version is retrieved automatically from composer.lock)
                'acme/contao-my-bundle'
            );
        }
    }
    // ...
}
```

#### Add a new entry from the context of a content element

```php
class MyContentElement {
    // ...
    public function compile() {
        // this represents your function for sending the opt in email
        $success = $this->sendOptInEmail($firstname, $lastname, $email);

        // only create a protocol entry if the email has indeed been sent
        if ($success)
        {
            $protocolManager = new \HeimrichHannot\Privacy\Manager\ProtocolManager();
            $protocolManager->addEntryFromContentElement(
                // the type of action
                \HeimrichHannot\Privacy\Backend\ProtocolEntry::TYPE_FIRST_OPT_IN,
                // the id of your destination protocol archive
                1,
                // the data you want to add to the protocol entry to be created
                // CAUTION: Do NOT store personal data for which you don't have the user's permission!
                [
                    'firstname' => $firstname,
                    'lastname'  => $lastname,
                    'email'     => $email
                ],
                // the \Contao\ContentElement instance you're calling from
                $this,
                // optional: composer package name of the bundle your content element lives in (version is retrieved automatically from composer.lock)
                'acme/contao-my-bundle'
            );
        }
    }
    // ...
}
```

#### Add a new entry from a general context

```php
class MyClass {
    // ...
    public function someFunction() {
        // this represents your function for sending the opt in email
        $success = $this->sendOptInEmail($firstname, $lastname, $email);

        // only create a protocol entry if the email has indeed been sent
        if ($success)
        {
            $protocolManager = new \HeimrichHannot\Privacy\Manager\ProtocolManager();
            $protocolManager->addEntry(
                // the type of action
                \HeimrichHannot\Privacy\Backend\ProtocolEntry::TYPE_FIRST_OPT_IN,
                // the id of your destination protocol archive
                1,
                // the data you want to add to the protocol entry to be created
                // CAUTION: Do NOT store personal data for which you don't have the user's permission!
                [
                    'firstname' => $firstname,
                    'lastname'  => $lastname,
                    'email'     => $email
                ],
                // optional: composer package name of the bundle your module lives in (version is retrieved automatically from composer.lock)
                'acme/contao-my-bundle'
            );
        }
    }
    // ...
}
```

## FAQ

1. Question: Why isn't there the protocol entry types "opt-in" and "opt-out"?<br>
   Answer: We strongly encourage you to build __double__ opt-in/out features since courts frequently decide that single opt-in/out processes are NOT a sufficient solution.
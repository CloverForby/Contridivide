# com.octopus8.contridivide

![Screenshot](/images/screenshot.png)


The extension is licensed under [AGPL-3.0](LICENSE.txt).

> [!CAUTION]
> This extension is in it's alpha-beta stage, if you have any errors please send a report!

# Overview

A Custom Civicrm extension that upon enabling will create a custom field called "Reciept ID" for contributions.

When a new contribution is made, Reciept ID will be filled with a unique ID, based on whether the Financial Type chosen is deductable or not.

If it's tax deductable, it starts with "TD_", if not it starts with "NT_", the number that follows is an auto-increment that is seperate from the other deductable type 

(Example: if there's an NT_12, the new Contribution id will be NT_13, the auto increment will not be affected by reciept id's starting with TD_)


# Requirements

- CiviContribute

# Installation

- Download the file and drop it into your extensions folder

# Known Bugs

TBA
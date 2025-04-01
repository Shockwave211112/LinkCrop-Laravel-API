<?php

return [
    'auth' => [
        'social_registered' => 'Please log in through the social network or reset your password.',
        'mismatch' => 'Email/login and password mismatch.',
        'email_already_verified' => 'Email already verified!',
        'expired' => 'Link expired.',
        'social' => 'An error occurred during authorization via :provider',
        'lang' => 'Unknown language.',
    ],
    'users' => [
        'not_found' => 'User not found.',
    ],
    'links' => [
        'not_found' => 'Link not found.',
        'group_permissions' => 'You dont have permissions to add link in this group.',
        'group_full' => 'Please select other group. Max count of links reached.',
        'max_count' => 'Maximum of links count reached.',
        'permissions' => 'You dont have permissions to interact with this link.',
    ],
    'groups' => [
        'not_found' => 'Group not found.',
        'permissions' => 'You dont have permissions to interact with this group.',
        'max_count' => 'Maximum of groups count reached.',
        'count_edit' => 'You dont have permissions to change count.',
        'delete_last' => 'You cant delete the last one group.',
        'delete_last_parent' => 'You cannot delete a group that is the only one in one of the links.',
    ],
    'permissions' => [
        'not_found' => 'Permission not found.',
    ],
    'model' => [
        'not_found' => ':model not found.',
    ],
    'unknown' => 'Unknown error.',
];

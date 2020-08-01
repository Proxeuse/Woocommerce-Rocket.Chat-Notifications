# Woocommerce-Rocket.Chat-Notifications
This WordPress plugin notifies you of new orders placed by your customers through one of your Rocket.Chat channels. The plugin uses the `woocommerce_thankyou` hook and therefore only runs if a customer places a order in the frontend.
Setting up the plugin must be done manually and cannot be done in the WordPress dashboard. However, it is a simple process that will take you less than 5 minutes to complete.

## Installation
Download the GitHub repository by cloning it or [downloading it as a zip file](https://github.com/Proxeuse/Woocommerce-Rocket.Chat-Notifications/archive/master.zip). Then extract the folder and upload the `woocommerce-rocket-chat-order-notifications` folder to your `/wp-content/plugins/` directory. The plugin is now installed but not yet configured.

## Configuration
Before you can edit the `woocommerce-rocket-chat-order-notifications.php` file and enter your credentials you should have setup a (seperate) user for your Rocket.Chat instance. We assume that you have created a user with the BOT role and have access to the username, email and password.
Firstly you should open the `woocommerce-rocket-chat-order-notifications.php` so that you can edit it. You should then scroll down to line 27 where the variables are set. Replace the existing Rocket.Chat URL with your own (e.g. [chat.proxeuse.com](https://chat.proxeuse.com/)). Please **do not** enter a trailing slash, you can however enter a port if your Rocket.Chat instance doesn't use the regular HTTP(s) ports (80 or 443). Then enter the username and password of the user that you've created before. Then enter a channel (#) or direct-message (@) at the `$rocketChatChannel` variable. More information about the `$rocketChatChannel` variable can be found here: [https://docs.rocket.chat/api/rest-api/methods/chat/postmessage](https://docs.rocket.chat/api/rest-api/methods/chat/postmessage). If your user has the BOT role you can assign an Alias for the user, if your user doesn't have the permissions please enter two quotes followed by a semicolon `"";`
That should be it for the configuration part. If you are a more experienced PHP developer/user you can edit other things such as the message send to the Rocket.Chat API.

## Testing
In order for you to test the new plugin we recommend you to uncomment (add to slashes in front of) line 137 `$order->update_meta_data( '_thankyou_action_done', true );`
If you have made changes to the Module settings you can refresh the Thank You page to post a new message in your Rocket.Chat channel.
You can create a 100% shortcode for your user to be able to test the plugin functionality without actually having to pay.

## Troubleshooting
Do you have troubles with using the plugin? [Submit an Issue on GitHub](https://github.com/Proxeuse/Woocommerce-Rocket.Chat-Notifications/issues/new) or contact us directly at [info@proxeuse.com](mailto:info@proxeuse.com).
You can also use `ECHO` statements to output text to the Thank You page.

## Contributors
[@lomars](https://github.com/lomars) - Thank you for writing the original [WooCommerce hook](https://stackoverflow.com/a/42533543) that allowed us to further develop the plugin.

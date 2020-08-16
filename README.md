# ph-wc-api-tester
Tests your WooCommerce Site's ability to process PayHere Payment Notifications.

## Usage Instructions ##

#### 1. Create a Request Bin ###
![Step One](https://github.com/Thisura98/ph-wc-api-tester/blob/master/one_setup_rbin.png?raw=true)

Creat a free request bin and make note of the `Endpoint` for your new bin.

#### 2. Setup the Plugin ####
![Step Two](https://github.com/Thisura98/ph-wc-api-tester/blob/master/two_setup_plugin.png)

Upload the folder named, 'payhere-wcapi-tester' to the following location on your Wordpress Installation.

```
<wp_root>/wp-content/plugins
```

Navigate to WP Admin > Plugins and activate the plugin. Once activate, click settings and you will see a page similar to the screenshot above.

Tick 'Enabled', and fill in the `Endpoint` you made note of in Step one. Click save changes.

#### 3. Test Requests ####

Open a [Postman](https://www.postman.com/) instance and use one of the "Site URL (POST)" links to send a request to your Wordpress Site.

Now examine your Request Bin!

If you see an Entry in the request lists on the left side, your website is capable of receiving PayHere Notifications.

__NOTE:__
Remember to make sure that as of PayHere WooCommerce Plugin version `1.0.9`, testing the second "Site URL (POST)" link should create a successful POST entry in your Request Bin.

The First link is only for debuggin. The actual link used within the plugin is the second one.

#### 4. Troubleshooting ####

If you experience different results than the ones mentioned in Step Three (above), contact [techsupport@payhere.lk](mailto:techsupport@payhere.lk) for assistance.

If you are developer, you can check the following.
1. Make sure you used the second link as __NOTED__ in Step Three (above).
2. Contact your hosting provider to see if they have blocked requests from outside of the current domain.
3. Try turning off Firewall, SSL, etc. Plugins in Wordpress.
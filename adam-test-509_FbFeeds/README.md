# Social Plugin
#### What is this doing?
Social Plugin is WPBakery compatible plugins with the following shortcodes:
*  **Social Icons** - Displaying selected social icons (Facebook and/or Twitter) with specified page id
*  **Twitter Feeds** - Displaying styled widget with posts from specified page
*  **Facebook Feeds** - Displaing styled widget with posts via Facebook API from specified page

#### How is it works? 
###### Social Icons
Selecting icon type at the same time is selected link base (for example "https://facebook.com/" for type="facebook") and icon from Font-Awesome. Page ID is added to the and of link
so it redirects to the specify page adress.
Style set proper styling to display icons verticaly or horizontaly.

###### Twitter Feeds
This shortcode is more complicated. Firstly after setting page id from which will be get posts it connects with this page and by DOM it is extracted feeds with contents, links or publish date.
This data is in json structure. It's decoded and then wrapped by nice layout.
So that you don't always connect to Twitter when refreshing the page it is set cache (default for 300 sec).  Between widget refreshing html is saved in database.

###### Facebook Feeds
It's work similary like Twitter Feeds with one diference. It's not taking feeds via DOM but it connect with Facebook API.

#### External Library
This plugin use only two external librarys:
*  Font-Awesome - for social media icons
*  Tweet2json - to take feeds from twitter page

## How to
You can add your shortcodes in two ways. First one is via WPBakery, second one handwrite it in post or page.

shortcodes:

`[ct_social style="horizontal/vertical" type_1="facebook/twitter" label_1="label-1" id_1="page-1-ID" type_2="facebook/twitter" label_2="label-2" id_2="page-2-ID"]`

`[ct_twitter style="light/dark" embed_images=true/false limit=21 cache=300 id="someID"]`

`[ct_facebook style="light/dark" embed_images=true/false limi=5 length_limit=100 cache=300 id="pageID" access_token="fbDevPageAccessToken"]`

In WPBakery you have to click on "Add new element" then click "CT Test Plugins" tab and choose widget. After this should open window with fields to fill. Save changes and done.

## Generating Access Token
For **Facebook Feed** widget it is needed access token. To generate it you have to log in to facebook developer console: https://developers.facebook.com/
Then create new App. After this go to **Tools** tab (on the top of page, next to Docs) and go to **Graph API Explorer**.
In Graph Api Explorer chose your api and thene **Get Token**->**Page Access Tokens**. In **Select Permissions** select **manage_pages**. Then **Get Access Token**. Done.
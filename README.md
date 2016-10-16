# Metatag

e107 (v2) plugin - This plugin allows you to automatically provide structured metadata, aka "meta tags", about a website.

    This plugin is under active development, please wait for release!!!

### Features

Metatag plugin provides a way:
- to set global meta tags
- to set default meta tags for each entity types (news, page, front page) integrated to this plugin
- to set custom meta tags for each entities (news item, page item) by extending Admin UI with "Metatag" tab

(see screenshots below)

Furthermore, it provides:
- e_metatag.php addon file, so you can integrate your custom pages used by your plugins

### You can use the following meta tags

#### Basic tags

- **title** (Title) - The text to display in the title bar of a visitor's web browser when they view this page. This meta tag may also be used as the title of the page when a visitor bookmarks or favorites this page.
- **description** (Description) - A brief and concise summary of the page's content, preferably 150 characters or less. The description meta tag may be used by search engines to display a snippet about the page in search results.
- **abstract** (Abstract) - A brief and concise summary of the page's content, preferably 150 characters or less. The abstract meta tag may be used by search engines for archiving purposes.
- **keywords** (Keywords) - A comma-separated list of keywords about the page. This meta tag is not supported by most search engines anymore.

#### Advanced tags

- **robots** - Robots - **Options:** follow, index, noarchive, nofollow, noimageindex, noindex, noodp, nosnippet, notranslate, noydir
  - **follow**: Allow search engines to follow links on this page (assumed).
  - **index**: Allow search engines to index this page (assumed).
  - **noarchive**: Prevents cached copies of this page from appearing in search results. 
  - **nofollow**: Prevents search engines from following links on this page. 
  - **noimageindex**: Prevent search engines from indexing images on this page. 
  - **noindex**: Prevents search engines from indexing this page. 
  - **noodp**: Blocks the [Open Directory Project](http://www.dmoz.org/) description from appearing in search results.
  - **nosnippet**: Prevents descriptions from appearing in search results, and prevents page caching. 
  - **notranslate**: Prevent search engines from offering to translate this page in search results. 
  - **noydir**: Prevents Yahoo! from listing this page in the [Yahoo! Directory](http://dir.yahoo.com/).
- **news_keywords** (Google News Keywords) - A comma-separated list of keywords about the page. This meta tag is used as an indicator in [Google News](https://support.google.com/news/publisher/answer/68297?hl=en).
- **standout** (Google Standout) - Highlight standout journalism on the web, especially for breaking news; used as an indicator in [Google News](https://support.google.com/news/publisher/answer/191283?hl=en&ref_topic=2484650). Warning: Don't abuse it, to be used a maximum of 7 times per calendar week!
- **rating** (Content rating) - Used to indicate the intended audience for the content. **Options:** general, mature, restricted, 14 years, safe for kids
- **referrer** (Referrer policy) - Indicate to search engines and other page scrapers whether or not links should be followed. [See the W3C specifications for further details](https://w3c.github.io/webappsec-referrer-policy/). **Options:** no-referrer, origin, no-referrer-when-downgrade, origin-when-cross-origin, unsafe-url
- **generator** (Generator) - Describes the name and version number of the software or publishing tool used to create the page.
- **rights** (Rights) - Details about intellectual property, such as copyright or trademarks; does not automatically protect the site's content or intellectual property.
- **image_src** (Image) - An image associated with this page, for use as a thumbnail in social networks and other services. This will be able to extract the URL from an image field.
- **canonical** (Canonical URL) - Preferred page location or URL to help eliminate duplicate content for search engines. For example, if content is available with SEF URL and general URL too.
- **shortlink** (Shortlink URL) - A brief URL, often created by a URL shortening service.
- **publisher** (Publisher URL) - 
- **author** (Author URL) - Used by some search engines to confirm authorship of the content on a page. Should be either the full URL for the author's Google+ profile page or a local page with information about the author.
- **original-source** (Original Source) - Used to indicate the URL that broke the story, and can link to either an internal URL or an external source. If the full URL is not known it is acceptable to use a partial URL or just the domain name.
- **prev** (Previous page URL) - Used for paginated content. Meet Google recommendations to [indicate paginated content](https://support.google.com/webmasters/answer/1663744) by providing URL with rel="prev" link.
- **next** (Next page URL) - Used for paginated content. Meet Google recommendations to [indicate paginated content](https://support.google.com/webmasters/answer/1663744) by providing URL with rel="next" link.
- **geo.position** (Geo position) - Geo-spatial information in "latitude;longitude" format, e.g. "50.167958;-97.133185"; [see Wikipedia for details](https://en.wikipedia.org/wiki/Geotagging#HTML_pages).
- **geo.placename** (Geo place name) - A location's formal name.
- **geo.region** (Geo region) - A location's two-letter international country code, with an optional two-letter region, e.g. "US-NH" for New Hampshire in the USA.
- **icbm** (ICBM) - Geo-spatial information in "latitude, longitude" format, e.g. "50.167958, -97.133185"; [see Wikipedia for details](https://en.wikipedia.org/wiki/Intercontinental_ballistic_missile).
- **refresh** (Refreshing) - The number of seconds to wait before refreshing the page. May also force redirect to another page using the format "5; url=http://example.com/", which would be triggered after five seconds.

#### Open Graph

The [Open Graph meta tags](http://ogp.me/) are used control how Facebook, Pinterest, LinkedIn and other social networking sites interpret the site's content.

- **og:site_name** (Sitename) - A human-readable name for the site, e.g., IMDb.
- **og:type** (Content type) - The type of the content, e.g., movie. **Options by groups:** 
  - Activities
    - "activity"
    - "sport"
  - Businesses
    - "bar"
    - "company"
    - "cafe"
    - "hotel"
    - "restaurant"
  - Groups
    - "cause"
    - "sports_league"
    - "sports_team"
  - Organizations
    - "band"
    - "government"
    - "non_profit"
    - "school"
    - "university"
  - Humans
    - "actor"
    - "athlete"
    - "author"
    - "director"
    - "musician"
    - "politician"
    - "profile"
    - "public_figure"
  - Places
    - "city"
    - "country"
    - "landmark"
    - "state_province"
  - Products and Entertainment
    - "album"
    - "book"
    - "drink"
    - "food"
    - "game"
    - "product"
    - "song"
    - "video.movie"
    - "video.tv_show"
    - "video.episode"
    - "video.other"
  - Website
    - "website"
    - "article"
- **og:url** (Page URL) - Preferred page location or URL to help eliminate duplicate content for search engines, e.g., http://www.imdb.com/title/tt0117500/.
- **og:title** (Content Title) - The title of the content, e.g., The Rock.
- **og:determiner** (Content title determiner) - The word that appears before the content's title in a sentence.
- **og:description** (Content description) - A one to two sentence description of the content.
- **og:updated_time** (Content modification date & time) - The date this content was last modified, with an optional time value. Needs to be in [ISO 8601](https://en.wikipedia.org/wiki/ISO_8601) format. Can be the same as the 'Article modification date' tag.
- **og:see_also** (See also) - URLs to related content. Multiple values may be used, separated by a comma.
- **og:image** (Image) - The URL of an image which should represent the content. For best results use an image that is at least 1200 x 630 pixels in size, but at least 600 x 316 pixels is a recommended minimum. Supports PNG, JPEG and GIF formats. Should not be used if og:image:url is used. Note: if multiple images are added many services (e.g. Facebook) will default to the largest image, not the first one. Multiple values may be used, separated by a comma.
- **og:image:url** (Image URL) - A alternative version of og:image and has exactly the same requirements; only one needs to be used. Multiple values may be used, separated by a comma.
- **og:image:secure_url** (Secure image URL) - The secure URL (HTTPS) of an image which should represent the content. The image must be at least 50px by 50px and have a maximum aspect ratio of 3:1. Supports PNG, JPEG and GIF formats. All "http://" URLs will automatically be converted to "https://". Note: if multiple images are added many services (e.g. Facebook) will default to the largest image, not the first one. Multiple values may be used, separated by a comma.
- **og:image:type** (Image type) - The type of image referenced above. Should be either "image/gif" for a GIF image, "image/jpeg" for a JPG/JPEG image, or "image/png" for a PNG image. Note: there should be one value for each image, and having more than there are images may cause problems. Multiple values may be used, separated by a comma.
- **og:image:width** (Image width) - The width of the above image(s). Note: if both the unsecured and secured images are provided, they should both be the same size. Multiple values may be used, separated by a comma.
- **og:image:height** (Image height) - The height of the above image(s). Note: if both the unsecured and secured images are provided, they should both be the same size. Multiple values may be used, separated by a comma.
- **og:latitude** (Latitude)
- **og:longitude** (Longitude)
- **og:street_address** (Street)
- **og:locality** (Locality)
- **og:region** (Region)
- **og:postal_code** (Postal/ZIP code)
- **og:country_name** (Country)
- **og:email** (E-mail address)
- **og:phone_number** (Phone number)
- **og:fax_number** (Fax number)
- **og:locale** (Locale) - The locale these tags are marked up in, must be in the format language_TERRITORY. Default is en_US.
- **og:locale:alternate** (Alternative locales) - Other locales this content is available in, must be in the format language_TERRITORY, e.g. "fr_FR". Multiple values may be used, separated by a comma.
- **og:audio** (Audio URL) - The URL to an audio file that complements this object.
- **og:audio:secure_url** (Audio secure URL) - The secure URL to an audio file that complements this object. All "http://" URLs will automatically be converted to "https://".
- **og:audio:type** (Audio type) - The MIME type of the audio file. Examples include "application/mp3" for an MP3 file
- **og:video:url** (Video URL) - The URL to a video file that complements this object.
- **og:video:secure_url** (Video secure URL) - A URL to a video file that complements this object using the HTTPS protocol. All "http://" URLs will automatically be converted to "https://".
- **og:video:width** (Video with) - The width of the video.
- **og:video:height** (Video height) - The height of the video.
- **og:video:type** (Video type) - The MIME type of the video file. Examples include "application/x-shockwave-flash" for a Flash video, or "text/html" if this is a standalone web page containing a video.
- **video:actor** (Actor(s)) - Links to the Facebook profiles for actor(s) that appear in the video. Multiple values may be used, separated by a comma.
- **video:actor:role** (Actors' role) - The roles of the actor(s). Multiple values may be used, separated by a comma.
- **video:director** (Director(s)) - Links to the Facebook profiles for director(s) that worked on the video.
- **video:writer** (Scriptwriter(s)) - Links to the Facebook profiles for scriptwriter(s) for the video. Multiple values may be used, separated by a comma.
- **video:duration** (Video duration (seconds)) - The length of the video in seconds
- **video:release_date** (Release date) - The date the video was released.
- **video:tag** (Tags) - Tag words associated with this video. Multiple values may be used, separated by a comma.

#### Facebook

Meta tags used to integrate with Facebook's APIs. Most sites do not need to use these, they are primarily of benefit for sites using either the Facebook widgets, the Facebook Connect single-signon system, or are using Facebook's APIs in a custom way.

- **fb:admins** (Admins) - A comma-separated list of Facebook user IDs of people who are considered administrators or moderators of this page. Multiple values may be used, separated by a comma.
- **fb:app_id** (Application ID) - A comma-separated list of Facebook Platform Application IDs applicable for this site.

#### Twitter card

A set of meta tags specially for controlling the summaries displayed when content is shared on [Twitter](https://twitter.com/).

- **twitter:card** (Twitter card type) - **Options:** summary (Summary), summary_large_image (Summary with large image), photo (Photo), player (Media player), gallery (Gallery), app (Application), product (Product). Notes: no other fields are required for a Summary card, a Photo card requires the 'image' field, a Media player card requires the 'title', 'description', 'media player URL', 'media player width', 'media player height' and 'image' fields, a Summary Card with Large Image card requires the 'Summary' field and the 'image' field, a Gallery Card requires all the 'Gallery Image' fields, an App Card requires the 'iPhone app ID' field, the 'iPad app ID' field and the 'Google Play app ID' field, a Product Card requires the 'description' field, the 'image' field, the 'Label 1' field, the 'Data 1' field, the 'Label 2' field and the 'Data 2' field.
- **twitter:site** (Site's Twitter account) - The @username for the website, which will be displayed in the Card's footer; must include the @ symbol.
- **twitter:site:id** (Site's Twitter account ID) - The numerical Twitter account ID for the website, which will be displayed in the Card's footer.
- **twitter:creator** (Creator's Twitter account) - The @username for the content creator / author for this page, including the @ symbol.
- **twitter:creator:id** (Creator's Twitter account ID) - The numerical Twitter account ID for the content creator / author for this page.
- **twitter:url** (Page URL) - The permalink / canonical URL of the current page.
- **twitter:title** (Title) - The page's title, which should be concise; it will be truncated at 70 characters by Twitter. This field is required unless this the 'type' field is set to "photo".
- **twitter:description** (Description) - A description that concisely summarizes the content of the page, as appropriate for presentation within a Tweet. Do not re-use the title text as the description, or use this field to describe the general services provided by the website. The string will be truncated, by Twitter, at the word to 200 characters.
- **twitter:image** (Image URL) - The URL to a unique image representing the content of the page. Do not use a generic image such as your website logo, author photo, or other image that spans multiple pages. Images larger than 120x120px will be resized and cropped square based on longest dimension. Images smaller than 60x60px will not be shown. If the 'type' is set to Photo then the image must be at least 280x150px. This will be able to extract the URL from an image field.
- **twitter:app:country** (App Store Country) - If your application is not available in the US App Store, you must set this value to the two-letter country code for the App Store that contains your application.
- **twitter:app:name:iphone** (iPhone app name) - The name of the iPhone app.
- **twitter:app:id:iphone** (iPhone app ID) - String value, should be the numeric representation of your iPhone app's ID in the App Store.
- **twitter:app:url:iphone** (iPhone app's custom URL scheme) - The iPhone app's custom URL scheme (must include "://" after the scheme name).
- **twitter:app:name:ipad** (iPad app name) - The name of the iPad app.
- **twitter:app:id:ipad** (iPad app ID) - String value, should be the numeric representation of your iPad app's ID in the App Store.
- **twitter:app:url:ipad** (iPad app's custom URL scheme) - The iPad app's custom URL scheme (must include "://" after the scheme name).
- **twitter:app:name:googleplay** (Google Play app name) - The name of the app in the Google Play app store.
- **twitter:app:id:googleplay** (Google Play app ID) - String value, and should be the numeric representation of your app's ID in Google Play.
- **twitter:app:url:googleplay** (Google Play app's custom URL scheme) - The Google Play app's custom URL scheme (must include "://" after the scheme name).

#### Dublin Core Basic Tags

The Dublin Core Metadata Element Set, aka "Dublin Core meta tags", are a set of internationally standardized metadata tags used to describe content to make identification and classification of content easier; the standards are controlled by the [Dublin Core Metadata Initiative (DCMI)](http://dublincore.org/).

- **dcterms.title** (Title) - The name given to the resource.
- **dcterms.creator** (Creator) - An entity primarily responsible for making the resource. Examples of a Creator include a person, an organization, or a service. Typically, the name of a Creator should be used to indicate the entity.
- **dcterms.subject** () - The topic of the resource. Typically, the subject will be represented using keywords, key phrases, or classification codes. Recommended best practice is to use a controlled vocabulary. To describe the spatial or temporal topic of the resource, use the Coverage element.
- **dcterms.description** (Description) - An account of the resource. Description may include but is not limited to: an abstract, a table of contents, a graphical representation, or a free-text account of the resource.
- **dcterms.publisher** (Publisher) - An entity responsible for making the resource available. Examples of a Publisher include a person, an organization, or a service. Typically, the name of a Publisher should be used to indicate the entity.
- **dcterms.contributor** (Contributor) - An entity responsible for making contributions to the resource. Examples of a Contributor include a person, an organization, or a service. Typically, the name of a Contributor should be used to indicate the entity.
- **dcterms.date** (Date) - A point or period of time associated with an event in the lifecycle of the resource. Date may be used to express temporal information at any level of granularity. Recommended best practice is to use an encoding scheme, such as the W3CDTF profile of ISO 8601 [W3CDTF].
- **dcterms.type** (Type) - **Options:** Collection, Dataset, Event, Image, InteractiveResource, MovingImage, PhysicalObject, Service, Software, Sound, StillImage, Text. The nature or genre of the resource. Recommended best practice is to use a controlled vocabulary such as the DCMI Type Vocabulary [DCMITYPE]. To describe the file format, physical medium, or dimensions of the resource, use the Format element.
- **dcterms.format** (Format) - The file format, physical medium, or dimensions of the resource. Examples of dimensions include size and duration. Recommended best practice is to use a controlled vocabulary such as the list of Internet Media Types [MIME].
- **dcterms.identifier** (Identifier) - An unambiguous reference to the resource within a given context. Recommended best practice is to identify the resource by means of a string conforming to a formal identification system.
- **dcterms.source** (Source) - A related resource from which the described resource is derived. The described resource may be derived from the related resource in whole or in part. Recommended best practice is to identify the related resource by means of a string conforming to a formal identification system.
- **dcterms.language** (Language) - A language of the resource. Recommended best practice is to use a controlled vocabulary such as RFC 4646 [RFC4646].
- **dcterms.relation** (Contact) - A related resource. Recommended best practice is to identify the related resource by means of a string conforming to a formal identification system.
- **dcterms.coverage** (Coverage) - The spatial or temporal topic of the resource, the spatial applicability of the resource, or the jurisdiction under which the resource is relevant. Spatial topic and spatial applicability may be a named place or a location specified by its geographic coordinates. Temporal topic may be a named period, date, or date range. A jurisdiction may be a named administrative entity or a geographic place to which the resource applies. Recommended best practice is to use a controlled vocabulary such as the Thesaurus of Geographic Names [TGN]. Where appropriate, named places or time periods can be used in preference to numeric identifiers such as sets of coordinates or date ranges.
- **dcterms.rights** (Rights) - Information about rights held in and over the resource. Typically, rights information includes a statement about various property rights associated with the resource, including intellectual property rights.

#### Google+

A set of meta tags specially for controlling the summaries displayed when content is shared on [Google+](https://plus.google.com/).

- **itemtype** (Page type) - Schema type. [More schema info](http://schema.org/docs/schemas.html). **Options:** Article, Blog, Book, Event, LocalBusiness, Organization, Person, Product, Review
- **itemprop:name** (Title) - A Google+ title for the page being shared. Keep keywords towards the front.
- **itemprop:description** (Description) - Longer form description, you’ve 200 words here that can specifically reference your presence on Google+
- **itemprop:image** (Image URL) - The URL to a unique image representing the content of the page. Do not use a generic image such as your website logo, author photo, or other image that spans multiple pages. This will be able to extract the URL from an image field.

### Screenshot

![Screenshot 1](https://dl.dropboxusercontent.com/u/17751753/metatag/metatag01.png)

![Screenshot 1](https://dl.dropboxusercontent.com/u/17751753/metatag/metatag02.png)


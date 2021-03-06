# Post types and properties

[Post settings](/doc/doc-conf-post.md).

## Article

Apart from all the properties from _CreativeWork_ and _Thing_ that the [Article](https://schema.org/Article "https://schema.org/Article") Type inherit, they also possess three extra properties:

| ID  | From    | Property           | Description                                                                     |
| --- | ------- | ------------------ | ------------------------------------------------------------------------------- |
| A31 | WP-Core | **articleBody**    | The actual body of the Post.                                                    |
| A32 | WP-Core | **articleSection** | Articles may belong to one or more 'categories' such as Sports, Lifestyle, etc. |
| A33 | WP-Core | **wordCount**      | The number of words in the text of the Post.                                    |

Article is a parent type with several child types:

### AdvertiserContentArticle

[AdvertiserContentArticle](https://schema.org/AdvertiserContentArticle "https://schema.org/AdvertiserContentArticle") type uses the related properties from the Article type.

### Report

[Report](https://schema.org/Report "https://schema.org/Report") type uses the related properties from the Article type.

### SatiricalArticle

[SatiricalArticle](https://schema.org/SatiricalArticle "https://schema.org/SatiricalArticle") type uses the related properties from the Article type.

### SocialMediaPosting

[SocialMediaPosting](https://schema.org/SocialMediaPosting "https://schema.org/SocialMediaPosting") type uses the related properties from the Article type.

### BlogPosting

[BlogPosting](https://schema.org/BlogPosting "https://schema.org/BlogPosting") type uses the related properties from the Article type.

### LiveBlogPosting

[LiveBlogPosting](https://schema.org/LiveBlogPosting "https://schema.org/LiveBlogPosting") type uses the related properties from the Article type.

### DiscussionForumPosting

[DiscussionForumPosting](https://schema.org/DiscussionForumPosting "https://schema.org/DiscussionForumPosting") type uses the related properties from the Article type.

### TechArticle

[TechArticle](https://schema.org/TechArticle "https://schema.org/TechArticle") type uses the related properties from the Article type.

## General Article properties

The related properties from the [Creative Work](https://schema.org/CreativeWork "https://schema.org/CreativeWork") type that matters to the project are:

| ID  | From    | Property                    | Description                                                                                                            |
| --- | ------- | --------------------------- | ---------------------------------------------------------------------------------------------------------------------- |
| A04 | WP-Core | **headline**                | Title of the Post.                                                                                                     |
| A06 | WP-Core | **inLanguage** (NOT READY)  | The language of the post.                                                                                              |
| A10 | WP-Core | **keywords**                |	Keywords or tags used to describe this content. Multiple entries in a keywords list are typically delimited by commas. |
| A17 | WP-Core | **publisher** (logo - name) | By default the name of the domain is taken. If a SEO plugin is activated, it uses the Organization value.              |
| A21 | WP-Core | **author**                  | The author of this Post.                                                                                               |
| A23 | WP-Core | **editor**                  | The last user that modify this Post.                                                                                   |
| A24 | WP-Core | **dateCreated**             | The date on which the Post was created.                                                                                |
| A25 | WP-Core | **datePublished**           | Date of first Post.                                                                                                    |
| A26 | WP-Core | **dateModified**            | The date on which the Post was most recently modified.                                                                 |
| A29 | WP-Core | **thumbnailUrl**            | A thumbnail image relevant to the Post.                                                                                |

The related properties from the [Thing](https://schema.org/Thing "https://schema.org/Thing") type that matters to the project are:

| ID  | From    | Property | Description                  |
| --- | ------- | -------- | ---------------------------- |
| A30  | WP-Core | **image**    | Featured image of the Post*  |

* If no Featured image in the Post, user avatar will be the Post image.


---

[Readme](//Readme.md)

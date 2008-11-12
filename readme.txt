README:

well, the patch is pending in the core list...

This is an simple xclass.

The function setPageCacheContent is replaced by this extension.

The extension gets the expire-timestamp which is configured by typoScript etc.

If there is an tt_content element on that page (which is not hidden and not
deleted) the starttime and the endtime will be checked too.

If there is a starttime or an endtime which is between "now" (Rendering time)
and the expire-timestamp, the expire-timestamp will be set to that time.

Internally the expire-timestamp is checked everytime a page is requested.
If the page is in the cache and not expired, than the cache will be taken.
And without this extension, the starttime and endtime of the content elements
have no influence.

At this time, it checks only tt_content elements on that page.

Feedback is welcome - please use bugtracker on forge.typo3.org

ok, i started an manuall - please read that, 13.09.2008

thanks,
martin
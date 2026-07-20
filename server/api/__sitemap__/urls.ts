import { serverQueryContent } from '#content/server'
import { asSitemapUrl, defineSitemapEventHandler } from '#imports'

export default defineSitemapEventHandler(async (event) => {
  const contentEntries = await serverQueryContent(event).find()

  return contentEntries
    .filter((entry) => typeof entry._path === 'string' && entry._path.startsWith('/blog/'))
    .map((entry) =>
      asSitemapUrl({
        loc: entry._path,
        lastmod: entry.date,
      })
    )
})
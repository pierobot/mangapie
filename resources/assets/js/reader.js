/**
 *
 * @param mangaId
 * @param archiveId
 * @return {string}
 */
function key(mangaId, archiveId) {
    if (mangaId === undefined || archiveId === undefined)
        throw "Invalid key parameter";

    return "reader-" + mangaId + "-" + archiveId;
}

module.exports = {
    /**
     * Returns the reader data from the session storage.
     *
     * @param mangaId The id of the manga.
     * @param archiveId the id of the archive.
     * @return {any}
     */
    storageFind: function (mangaId, archiveId) {
        try {
            return JSON.parse(sessionStorage.getItem(key(mangaId, archiveId)));
        } catch (e) {
            alert("Invalid json object in session storage.");

            return null;
        }
    },

    /**
     * Stores the given object to the session storage.
     *
     * @param mangaId The id of the manga.
     * @param archiveId The id of the archive.
     * @param obj The object to store.
     * @return {boolean}
     */
    storagePut: function (mangaId, archiveId, obj) {
        try {
            sessionStorage.setItem(key(mangaId, archiveId), JSON.stringify(obj));

            return true;
        } catch (e) {
            console.log("Unable to store object in session storage.");

            return false;
        }
    }
};
"use strict";

module.exports = {
    history: {
        /**
         * Pushes the current URL onto the history stack and replaces the current URL with the given one.
         * 
         * @param {string} key The key to use.
         * @param {string} url The new URL to replace the current with.
         */
        pushReplace: function (key, url) {
            history.pushState(key, '');
            history.replaceState(key, '', url);
        }
    },

    sessionStorage: {
        /**
         * Returns the reader data from the session storage.
         *
         * @param key
         * @return {any}
         */
        find: function (key) {
            try {
                return JSON.parse(sessionStorage.getItem(key));
            } catch (e) {
                alert("Invalid json object in session storage.");

                return null;
            }
        },

        /**
         * Stores the given object to the session storage.
         *
         * @param key
         * @param obj The object to store.
         * @return {boolean}
         */
        put: function (key, obj) {
            try {
                sessionStorage.setItem(key, JSON.stringify(obj));

                return true;
            } catch (e) {
                console.log("Unable to store object in session storage.");

                return false;
            }
        }
    }
};

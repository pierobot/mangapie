<?php

use Illuminate\Database\Seeder;

use App\Genre;

class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Genre::count() > 0) {
            $this->command->getOutput()->writeln("Genres table is not empty, skipping...");
            return;
        }

        Genre::create([
            'name' => 'Action',
            'description' => 'A work typically depicting fighting, violence, chaos, and fast paced motion.'
        ]);

        Genre::create([
            'name' => 'Adult',
            'description' => 'Contains content that is suitable only for adults. Titles in this category may include prolonged scenes of intense violence and/or graphic sexual content and nudity.'
        ]);

        Genre::create([
            'name' => 'Adventure',
            'description' => 'If a character in the story goes on a trip or along that line, your best bet is that it is an adventure manga.  Otherwise, it\'s up to your personal prejudice on this case.'
        ]);

        Genre::create([
            'name' => 'Comedy',
            'description' => 'A dramatic work that is light and often humorous or satirical in tone and that usually contains a happy resolution of the thematic conflict.'
        ]);

        Genre::create([
            'name' => 'Doujinshi',
            'description' => 'Fan based work inspired by official anime or manga. For MangaUpdates, original works DO NOT fall under this category.'
        ]);

        Genre::create([
            'name' => 'Drama',
            'description' => 'A work meant to bring on an emotional response, such as instilling sadness or tension.'
        ]);

        Genre::create([
            'name' => 'Ecchi',
            'description' => 'Possibly the line between hentai and non-hentai, ecchi usually refers to fanservice put in to attract a certain group of fans.'
        ]);

        Genre::create([
            'name' => 'Fantasy',
            'description' => 'Anything that involves, but not limited to, magic, dream world, and fairy tales.'
        ]);

        Genre::create([
            'name' => 'Gender Bender',
            'description' => 'Girls dressing up as guys, guys dressing up as girls.. Guys turning into girls, girls turning into guys.. I think you get the picture.'
        ]);

        Genre::create([
            'name' => 'Harem',
            'description' => 'A series involving one male character and many female characters (usually attracted to the male character). A Reverse Harem is when the genders are reversed.'
        ]);

        Genre::create([
            'name' => 'Hentai',
            'description' => 'Adult sexual content in an illustrated form where the FOCUS of the manga is placed on sexually graphic acts.'
        ]);

        Genre::create([
            'name' => 'Historical',
            'description' => 'Having to do with old or ancient times.'
        ]);

        Genre::create([
            'name' => 'Horror',
            'description' => 'A painful emotion of fear, dread, and abhorrence; a shuddering with terror and detestation; the feeling inspired by something frightful and shocking.'
        ]);

        Genre::create([
            'name' => 'Josei',
            'description' => 'Literally "Woman". Targets women 18-30. Female equivalent to seinen. Unlike shoujo the romance is more realistic and less idealized. The storytelling is more explicit and mature.'
        ]);

        Genre::create([
            'name' => 'Lolicon',
            'description' => 'Representing a sexual attraction to young or under-age girls.'
        ]);

        Genre::create([
            'name' => 'Martial Arts',
            'description' => 'As the name suggests, anything martial arts related. Any of several arts of combat or self-defense, such as aikido, karate, judo, or tae kwon do, kendo, fencing, and so on and so forth.'
        ]);

        Genre::create([
            'name' => 'Mature',
            'description' => 'Contains subject matter which may be too extreme for people under the age of 17. Titles in this category may contain intense violence, blood and gore, sexual content and/or strong language.'
        ]);

        Genre::create([
            'name' => 'Mecha',
            'description' => 'A work involving and usually concentrating on all types of large robotic machines.'
        ]);

        Genre::create([
            'name' => 'Mystery',
            'description' => 'Usually an unexplained event occurs, and the main protagonist attempts to find out what caused it.'
        ]);

        Genre::create([
            'name' => 'Psychological',
            'description' => 'Usually deals with the philosophy of a state of mind, in most cases detailing abnormal psychology.'
        ]);

        Genre::create([
            'name' => 'Romance',
            'description' => 'Any love related story.  We will define love as between man and woman in this case. Other than that, it is up to your own imagination of what love is.'
        ]);

        Genre::create([
            'name' => 'School Life',
            'description' => 'Having a major setting of the story deal with some type of school.'
        ]);

        Genre::create([
            'name' => 'Sci-fi',
            'description' => 'Short for science fiction, these works involve twists on technology and other science related phenomena which are contrary or stretches of the modern day scientific world.'
        ]);

        Genre::create([
            'name' => 'Seinen',
            'description' => 'Manga and anime that specifically targets young adult males around the ages of 18 to 25 are seinen titles. The stories in seinen works appeal to university students and those in the working world. Typically the story lines deal with the issues of adulthood.'
        ]);

        Genre::create([
            'name' => 'Shotacon',
            'description' => 'Representing a sexual attraction to young or under-age boys.'
        ]);

        Genre::create([
            'name' => 'Shoujo',
            'description' => 'A work intended and primarily written for females.  Usually involves a lot of romance and strong character development.'
        ]);

        Genre::create([
            'name' => 'Shoujo Ai',
            'description' => 'Often synonymous with yuri, this can be thought of as somewhat less extreme.  \"Girl\'s Love\", so to speak.'
        ]);

        Genre::create([
            'name' => 'Shounen',
            'description' => 'A work intended and primarily written for males.  These  works usually involve fighting and/or violence.'
        ]);

        Genre::create([
            'name' => 'Shounen Ai',
            'description' => 'Often synonymous with yaoi, this can be thought of as somewhat less extreme.  \"Boy\'s Love\", so to speak.'
        ]);

        Genre::create([
            'name' => 'Slice of Life',
            'description' => 'As the name suggests, this genre represents day-to-day tribulations of one/many character(s). These challenges/events could technically happen in real life and are often -if not all the time- set in the present timeline in a world that mirrors our own.'
        ]);

        Genre::create([
            'name' => 'Smut',
            'description' => 'Deals with series that are considered profane or offensive, particularly with regards to sexual content'
        ]);

        Genre::create([
            'name' => 'Sports',
            'description' => 'As the name suggests, anything sports related.  Baseball, basketball, hockey, soccer, golf, and racing just to name a few.'
        ]);

        Genre::create([
            'name' => 'Supernatural',
            'description' => 'Usually entails amazing and unexplained powers or events which defy the laws of physics.'
        ]);

        Genre::create([
            'name' => 'Tragedy',
            'description' => 'Contains events resulting in great loss and misfortune.'
        ]);

        Genre::create([
            'name' => 'Yaoi',
            'description' => 'This work usually involves intimate relationships between men.'
        ]);

        Genre::create([
            'name' => 'Yuri',
            'description' => 'This work usually involves intimate relationships between women.'
        ]);
    }
}

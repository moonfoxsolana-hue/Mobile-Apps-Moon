package com.mysticnusa.app.navigation

sealed class Screen(val route: String) {
    object Splash : Screen("splash")
    object Login : Screen("login")
    object Register : Screen("register")
    object Home : Screen("home")
    object Profile : Screen("profile")
    object TokenHistory : Screen("token_history")
    object Claim : Screen("claim")
    object News : Screen("news")
    object NewsDetail : Screen("news_detail/{id}") {
        fun createRoute(id: Int) = "news_detail/$id"
    }
    object Stories : Screen("stories")
    object StoryDetail : Screen("story_detail/{id}") {
        fun createRoute(id: Int) = "story_detail/$id"
    }
    object Games : Screen("games")
    object TriviaGame : Screen("trivia_game")
    object LogicalGame : Screen("logical_game")
    object IntuitionGame : Screen("intuition_game")
    object TarotGame : Screen("tarot_game")
    object UlartanggaGame : Screen("ulartangga_game")
    object NgepetGame : Screen("ngepet_game")
    object Staking : Screen("staking")
}

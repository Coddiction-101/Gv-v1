const form = document.querySelector("#search-form");
const input = document.querySelector("#username");
const status = document.querySelector("#status");
const profile = document.querySelector("#profile");
const repositories = document.querySelector("#repositories");
const repoList = document.querySelector("#repo-list");

function showStatus(message = "") {
  status.textContent = message;
}

function renderUser(user) {
  document.querySelector("#avatar").src = user.avatar_url;
  document.querySelector("#avatar").alt = `${user.login}'s avatar`;
  document.querySelector("#name").textContent = user.name || user.login;
  document.querySelector("#bio").textContent = user.bio || "No bio provided.";
  document.querySelector("#github-link").href = user.html_url;
  profile.hidden = false;
}

function renderRepos(repos) {
  repoList.replaceChildren();
  repos.forEach((repo) => {
    const item = document.createElement("li");
    const link = document.createElement("a");
    const description = document.createElement("p");

    link.href = repo.html_url;
    link.target = "_blank";
    link.rel = "noreferrer";
    link.textContent = repo.name;
    description.textContent = `${repo.language || "No language"} · ${repo.stargazers_count} stars`;
    item.append(link, description);
    repoList.append(item);
  });
  repositories.hidden = false;
}

form.addEventListener("submit", async (event) => {
  event.preventDefault();
  const username = input.value.trim();
  if (!username) return;

  profile.hidden = true;
  repositories.hidden = true;
  showStatus("Loading...");

  try {
    const [userResponse, reposResponse] = await Promise.all([
      fetch(`https://api.github.com/users/${encodeURIComponent(username)}`),
      fetch(`https://api.github.com/users/${encodeURIComponent(username)}/repos?per_page=100&sort=updated`),
    ]);

    if (userResponse.status === 404) throw new Error("GitHub user not found.");
    if (!userResponse.ok || !reposResponse.ok) throw new Error("Could not load GitHub data. Try again later.");

    const [user, repos] = await Promise.all([userResponse.json(), reposResponse.json()]);
    renderUser(user);
    renderRepos(repos);
    showStatus(repos.length ? "" : "This user has no public repositories.");
  } catch (error) {
    showStatus(error instanceof TypeError ? "Network error: GitHub could not be reached." : error.message);
  }
});

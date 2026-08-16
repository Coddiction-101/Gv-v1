const form = document.querySelector("#search-form");
const input = document.querySelector("#username");
const searchButton = form.querySelector("button");

const statusMessage = document.querySelector("#status");
const profile = document.querySelector("#profile");
const repositories = document.querySelector("#repositories");
const repoList = document.querySelector("#repo-list");

const avatar = document.querySelector("#avatar");
const name = document.querySelector("#name");
const usernameDisplay = document.querySelector("#username-display");
const bio = document.querySelector("#bio");
const locationElement = document.querySelector("#location");
const company = document.querySelector("#company");
const publicRepos = document.querySelector("#public-repos");
const followers = document.querySelector("#followers");
const following = document.querySelector("#following");
const githubLink = document.querySelector("#github-link");
const repoCount = document.querySelector("#repo-count");

function showStatus(message = "", type = "error") {
  statusMessage.textContent = message;
  statusMessage.className = message ? type : "";
}

function setLoading(isLoading) {
  searchButton.disabled = isLoading;
  searchButton.textContent = isLoading ? "Searching..." : "Explore";
}

function formatNumber(number) {
  return new Intl.NumberFormat("en", {
    notation: "compact",
    maximumFractionDigits: 1,
  }).format(number);
}

function formatDate(date) {
  return new Intl.DateTimeFormat("en", {
    day: "numeric",
    month: "short",
    year: "numeric",
  }).format(new Date(date));
}

function renderUser(user) {
  avatar.src = user.avatar_url;
  avatar.alt = `${user.login}'s GitHub avatar`;

  name.textContent = user.name || user.login;
  usernameDisplay.textContent = `@${user.login}`;
  bio.textContent = user.bio || "This developer has not added a bio.";

  locationElement.textContent = user.location
    ? `Location: ${user.location}`
    : "";

  company.textContent = user.company
    ? `Company: ${user.company}`
    : "";

  publicRepos.textContent = formatNumber(user.public_repos);
  followers.textContent = formatNumber(user.followers);
  following.textContent = formatNumber(user.following);

  githubLink.href = user.html_url;
  profile.hidden = false;
}

function renderRepos(repos) {
  repoList.replaceChildren();
  repoCount.textContent = `${repos.length} public repositories`;

  if (repos.length === 0) {
    const emptyMessage = document.createElement("li");
    emptyMessage.className = "empty-state";
    emptyMessage.textContent = "This developer has no public repositories.";

    repoList.append(emptyMessage);
    repositories.hidden = false;
    return;
  }

  repos.forEach((repo) => {
    const item = document.createElement("li");
    item.className = "repo-item";

    const header = document.createElement("div");
    header.className = "repo-header";

    const link = document.createElement("a");
    link.className = "repo-name";
    link.href = repo.html_url;
    link.target = "_blank";
    link.rel = "noreferrer";
    link.textContent = repo.name;

    const language = document.createElement("span");
    language.className = "repo-language";
    language.textContent = repo.language || "Other";

    const description = document.createElement("p");
    description.className = "repo-description";
    description.textContent =
      repo.description || "No description provided.";

    const meta = document.createElement("p");
    meta.className = "repo-meta";

    const stars = document.createElement("span");
    stars.textContent = `★ ${formatNumber(repo.stargazers_count)}`;

    const forks = document.createElement("span");
    forks.textContent = `Forks ${formatNumber(repo.forks_count)}`;

    const updated = document.createElement("span");
    updated.textContent = `Updated ${formatDate(repo.updated_at)}`;

    header.append(link, language);
    meta.append(stars, forks, updated);
    item.append(header, description, meta);
    repoList.append(item);
  });

  repositories.hidden = false;
}

form.addEventListener("submit", async (event) => {
  event.preventDefault();

  const username = input.value.trim();

  if (!username) {
    showStatus("Enter a GitHub username.");
    return;
  }

  profile.hidden = true;
  repositories.hidden = true;

  setLoading(true);
  showStatus("Searching GitHub...", "loading");

  try {
    const encodedUsername = encodeURIComponent(username);

    const [userResponse, reposResponse] = await Promise.all([
      fetch(`https://api.github.com/users/${encodedUsername}`),
      fetch(
        `https://api.github.com/users/${encodedUsername}/repos?per_page=100&sort=updated`
      ),
    ]);

    if (userResponse.status === 404) {
      throw new Error("GitHub user not found.");
    }

    if (userResponse.status === 403 || reposResponse.status === 403) {
      throw new Error(
        "GitHub API limit reached. Please try again later."
      );
    }

    if (!userResponse.ok || !reposResponse.ok) {
      throw new Error(
        "GitHub data could not be loaded. Please try again."
      );
    }

    const [user, repos] = await Promise.all([
      userResponse.json(),
      reposResponse.json(),
    ]);

    renderUser(user);
    renderRepos(repos);
    showStatus("");

    profile.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  } catch (error) {
    const message =
      error instanceof TypeError
        ? "Network error: GitHub could not be reached."
        : error.message;

    showStatus(message);
  } finally {
    setLoading(false);
  }
});

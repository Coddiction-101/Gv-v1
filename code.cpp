class Solution {
public:
    string removeOuterParentheses(string s) {

        string answer = "";
        int depth = 0;

        for(int i = 0; i < s.size(); i++) {

            if(s[i] == '(') {

               if(depth > 0)
                answer += s[i];

                depth++;

            }
            else {

                depth--;

                 if(depth > 0)
                answer += ')';

            }
        }

        return answer;
    }
};

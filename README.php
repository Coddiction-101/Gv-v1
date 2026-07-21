 FUNCTION isSubsequence(s, t)

    i = 0
    j = 0

    WHILE i < length of s AND j < length of t

        IF s[i] == t[j]
            i = i + 1
        END IF

        j = j + 1

    END WHILE

    RETURN i == length of s

END FUNCTION
